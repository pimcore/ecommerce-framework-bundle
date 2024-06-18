<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker;

use Doctrine\DBAL\Connection;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\FindologicConfigInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use Pimcore\Logger;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property FindologicConfigInterface $tenantConfig
 *
 * @method FindologicConfigInterface getTenantConfig()
 */
class DefaultFindologic extends AbstractMockupCacheWorker implements WorkerInterface, BatchProcessingWorkerInterface
{
    const STORE_TABLE_NAME = 'ecommerceframework_productindex_store_findologic';

    const EXPORT_TABLE_NAME = 'ecommerceframework_productindex_export_findologic';

    const MOCKUP_CACHE_PREFIX = 'ecommerce_mockup_findologic';

    /**
     * findologic supported fields
     *
     */
    protected array $supportedFields = [
        'id', 'ordernumber', 'name', 'summary', 'description', 'price',
    ];

    protected \SimpleXMLElement $batchData;

    protected LoggerInterface $logger;

    public function __construct(FindologicConfigInterface $tenantConfig, Connection $db, EventDispatcherInterface $eventDispatcher, LoggerInterface $pimcoreEcommerceFindologic)
    {
        $this->logger = $pimcoreEcommerceFindologic;
        parent::__construct($tenantConfig, $db, $eventDispatcher);
    }

    /**
     * creates or updates necessary index structures (like database tables and so on)
     *
     */
    public function createOrUpdateIndexStructures(): void
    {
        $this->createOrUpdateStoreTable();
    }

    /**
     * deletes given element from index
     *
     *
     */
    public function deleteFromIndex(IndexableInterface $object): void
    {
        $this->doDeleteFromIndex($object->getId(), $object);
    }

    /**
     * updates given element in index
     *
     *
     */
    public function updateIndex(IndexableInterface $object): void
    {
        if (!$this->tenantConfig->isActive($object)) {
            Logger::info("Tenant {$this->name} is not active.");

            return;
        }

        $this->prepareDataForIndex($object);
        $this->fillupPreparationQueue($object);
    }

    protected function doUpdateIndex(int $objectId, array $data = null, array $metadata = null): void
    {
        $xml = $this->createXMLElement();

        $xml->addAttribute('id', (string) $objectId);
        $xml->addChild('allOrdernumbers')
            ->addChild('ordernumbers');
        $xml->addChild('names');
        $xml->addChild('summaries');
        $xml->addChild('descriptions');
        $xml->addChild('prices');
        $xml->addChild('allAttributes')
            ->addChild('attributes');

        $attributes = $xml->allAttributes->attributes;

        // add optional fields
        if (array_key_exists('salesFrequency', $data['data'])) {
            $xml->addChild('salesFrequencies')
                ->addChild('salesFrequency', $data['data']['salesFrequency'])
            ;
        }
        if (array_key_exists('dateAdded', $data['data'])) {
            $xml->addChild('dateAddeds')
                ->addChild('dateAdded', date('c', $data['data']['dateAdded']))
            ;
        }

        /**
         * Adds a child with $value inside CDATA
         *
         * @param \SimpleXMLElement $parent
         * @param string $name
         * @param string|null $value
         *
         * @return \SimpleXMLElement
         */
        $addChildWithCDATA = function (\SimpleXMLElement $parent, string $name, string $value = null) {
            $new_child = $parent->addChild($name);

            if ($new_child !== null) {
                $node = dom_import_simplexml($new_child);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection($value));
            }

            return $new_child;
        };

        // add default data
        foreach ($data['data'] as $field => $value) {
            // skip empty values
            if ((string)$value === '' || (is_array($value) && empty($value))) {
                continue;
            }
            $value = is_string($value) ? htmlspecialchars(strip_tags($value)) : $value;

            if (in_array($field, $this->supportedFields)) {
                // supported field
                switch ($field) {
                    case 'ordernumber':
                        $parent = $xml->allOrdernumbers->ordernumbers;
                        $parent->addChild('ordernumber', $value);

                        break;

                    case 'name':
                        $parent = $xml->names;
                        $parent->addChild('name', $value);

                        break;

                    case 'summary':
                        $parent = $xml->summaries;
                        $parent->addChild('summary', $value);

                        break;

                    case 'description':
                        $parent = $xml->descriptions;
                        $parent->addChild('description', $value);

                        break;

                    case 'price':
                        $parent = $xml->prices;
                        $parent->addChild('price', $value);

                        break;
                }
            } else {
                // unsupported, map all to attributes
                switch ($field) {
                    // richtige reihenfolge der kategorie berücksichtigen
                    case 'categoryIds':
                        $value = trim($value, ',');
                        if ($value) {
                            $attribute = $attributes->addChild('attribute');
                            $attribute->addChild('key', 'cat');
                            $values = $attribute->addChild('values');
                            $categories = explode(',', $value);

                            foreach ($categories as $c) {
                                $categoryIds = [];

                                $currentCategory = Concrete::getById((int) $c);
                                while ($currentCategory instanceof AbstractCategory) {
                                    $categoryIds[$currentCategory->getId()] = $currentCategory->getId();

                                    if ($currentCategory->getOSProductsInParentCategoryVisible()) {
                                        $currentCategory = $currentCategory->getParent();
                                    } else {
                                        $currentCategory = null;
                                    }
                                }

                                $values->addChild('value', implode('_', array_reverse($categoryIds, true)));
                            }
                        }

                        break;

                    default:
                        $attribute = $attributes->addChild('attribute');
                        $attribute->addChild('key', $field);
                        $values = $attribute->addChild('values');

                        if (!is_array($value)) {
                            $addChildWithCDATA($values, 'value', $value);
                        } else {
                            foreach ($value as $_item) {
                                $values->addChild('value', $_item);
                            }
                        }
                }
            }
        }

        // add relations
        $groups = [];
        foreach ($data['relations'] as $relation) {
            $groups[$relation['fieldname']][] = $relation['dest'];
        }
        foreach ($groups as $name => $values) {
            $attribute = $attributes->addChild('attribute');
            $attribute->addChild('key', $name);
            $v = $attribute->addChild('values');

            foreach ($values as $value) {
                $v->addChild('value', $value);
            }
        }

        // update export item
        if ($data['data']['active'] === true) {
            // update export item
            $this->updateExportItem($objectId, $xml);
        } else {
            // delete from export
            $this->db->executeQuery(sprintf('DELETE FROM %1$s WHERE id = %2$d', $this->getExportTableName(), $objectId));
        }

        // create / update mockup cache
        $this->saveToMockupCache($objectId, $data);
    }

    protected function doDeleteFromIndex(int $subObjectId, IndexableInterface $object = null): void
    {
        $this->db->executeQuery(sprintf('DELETE FROM %1$s WHERE id = %2$d', $this->getExportTableName(), $subObjectId));
        $this->db->executeQuery(sprintf('DELETE FROM %1$s WHERE id = %2$d', $this->getStoreTableName(), $subObjectId));
    }

    protected function updateExportItem(int $objectId, \SimpleXMLElement $item): void
    {
        // save
        $query = <<<SQL
INSERT INTO {$this->getExportTableName()} (`id`, `shop_key`, `data`, `last_update`)
VALUES (:id, :shop_key, :data, now())
ON DUPLICATE KEY UPDATE `data` = VALUES(`data`), `last_update` = VALUES(`last_update`)
SQL;
        $this->db->executeQuery($query, [
            'id' => $objectId, 'shop_key' => $this->getTenantConfig()->getClientConfig('shopKey'), 'data' => str_replace('<?xml version="1.0"?>', '', $item->saveXML()),
        ]);
    }

    protected function getStoreTableName(): string
    {
        return self::STORE_TABLE_NAME;
    }

    protected function getMockupCachePrefix(): string
    {
        return self::MOCKUP_CACHE_PREFIX;
    }

    protected function getExportTableName(): string
    {
        return self::EXPORT_TABLE_NAME;
    }

    protected function createXMLElement(): \SimpleXMLElement
    {
        return new \SimpleXMLElement('<?xml version="1.0"?><item />');
    }

    public function getProductList(): \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\DefaultFindologic
    {
        return new \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\DefaultFindologic($this->getTenantConfig(), $this->logger);
    }
}
