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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition\Attribute;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use Pimcore\Model\DataObject;

abstract class AbstractConfig implements ConfigInterface
{
    protected string $tenantName;

    protected array $attributeConfig = [];

    protected array $searchAttributeConfig = [];

    protected ?AttributeFactory $attributeFactory = null;

    protected array $attributes = [];

    protected array $searchAttributes = [];

    protected array $filterTypes = [];

    protected ?WorkerInterface $tenantWorker = null;

    protected ?array $filterTypeConfig = null;

    protected array $options = [];

    /**
     * @param array[]|Attribute[] $attributes
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        string $tenantName,
        array $attributes = [],
        array $searchAttributes = [],
        array $filterTypes = [],
        array $options = [],
    ) {
        $this->tenantName = $tenantName;

        $this->attributeConfig = $attributes;
        $this->searchAttributeConfig = $searchAttributes;

        $this->filterTypes = $filterTypes;

        $this->attributeFactory = $attributeFactory;
        $this->buildAttributes($this->attributeConfig);

        foreach ($this->searchAttributeConfig as $searchAttribute) {
            $this->addSearchAttribute($searchAttribute);
        }

        $this->processOptions($options);
    }

    /**
     * Attribute configuration
     *
     */
    public function getAttributeConfig(): array
    {
        return $this->attributeConfig;
    }

    protected function buildAttributes(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            if ($attribute instanceof Attribute) {
                $this->addAttribute($attribute);
            } elseif (is_array($attribute)) {
                $attribute = $this->attributeFactory->createAttribute($attribute);
                $this->addAttribute($attribute);
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Wrong type for attribute. Expected Attribute or array, got "%s"',
                    is_object($attribute) ? get_class($attribute) : gettype($attribute)
                ));
            }
        }
    }

    protected function addAttribute(Attribute $attribute): void
    {
        $this->attributes[$attribute->getName()] = $attribute;
    }

    protected function addSearchAttribute(string $searchAttribute): void
    {
        if (!isset($this->attributes[$searchAttribute])) {
            throw new \InvalidArgumentException(sprintf(
                'The search attribute "%s" in product index tenant "%s" is not defined as attribute',
                $searchAttribute,
                $this->tenantName
            ));
        }

        $this->searchAttributes[] = $searchAttribute;
    }

    protected function processOptions(array $options): void
    {
        // noop - to implemented by configs supporting options
    }

    public function setTenantWorker(WorkerInterface $tenantWorker): void
    {
        $this->checkTenantWorker($tenantWorker);
        $this->tenantWorker = $tenantWorker;
    }

    /**
     * Checks if tenant worker matches prerequisites (config wrapped in worker is this instance and instance has no
     * worker set yet).
     *
     */
    protected function checkTenantWorker(WorkerInterface $tenantWorker): void
    {
        if (null !== $this->tenantWorker) {
            throw new \LogicException(sprintf('Worker for tenant "%s" is already set', $this->tenantName));
        }

        // make sure the worker is the one working on this config instance
        if ($tenantWorker->getTenantConfig() !== $this) {
            throw new \LogicException('Worker config does not match the config the worker is about to be set to');
        }
    }

    public function getTenantWorker(): WorkerInterface
    {
        // the worker is expected to call setTenantWorker as soon as possible
        if (null === $this->tenantWorker) {
            throw new \RuntimeException('Tenant worker is not set.');
        }

        return $this->tenantWorker;
    }

    public function getTenantName(): string
    {
        return $this->tenantName;
    }

    /**
     * Returns configured attributes for product index
     *
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns full text search index attribute names for product index
     *
     */
    public function getSearchAttributes(): array
    {
        return $this->searchAttributes;
    }

    /**
     * return all supported filter types for product index
     *
     */
    public function getFilterTypeConfig(): ?array
    {
        return $this->filterTypeConfig;
    }

    public function isActive(IndexableInterface $object): bool
    {
        return true;
    }

    /**
     *
     * @return AbstractCategory[]
     */
    public function getCategories(IndexableInterface $object, int $subObjectId = null): array
    {
        return $object->getCategories();
    }

    /**
     * creates an array of sub ids for the given object
     * use that function, if one object should be indexed more than once (e.g. if field collections are in use)
     *
     *
     * @return IndexableInterface[]
     */
    public function createSubIdsForObject(IndexableInterface $object): array
    {
        return [$object->getId() => $object];
    }

    /**
     * checks if there are some zombie subIds around and returns them for cleanup
     *
     *
     */
    public function getSubIdsToCleanup(IndexableInterface $object, array $subIds): array
    {
        return [];
    }

    /**
     * creates virtual parent id for given sub id
     * default is getOSParentId
     *
     *
     */
    public function createVirtualParentIdForSubId(IndexableInterface $object, int $subId): int|string|null
    {
        return $object->getOSParentId();
    }

    /**
     * Gets object by id, can consider subIds and therefore return e.g. an array of values
     * always returns object itself - see also getObjectMockupById
     *
     * @param bool $onlyMainObject - only returns main object
     *
     */
    public function getObjectById(int $objectId, bool $onlyMainObject = false): ?DataObject
    {
        return DataObject::getById($objectId);
    }

    /**
     * Gets object mockup by id, can consider subIds and therefore return e.g. an array of values
     * always returns a object mockup if available
     *
     *
     */
    public function getObjectMockupById(int $objectId): ?IndexableInterface
    {
        $object = $this->getObjectById($objectId);
        if ($object instanceof IndexableInterface) {
            return $object;
        }

        return null;
    }

    /**
     * returns column type for id
     *
     *
     */
    public function getIdColumnType(bool $isPrimary): string
    {
        if ($isPrimary) {
            return "int(11) NOT NULL default '0'";
        } else {
            return 'int(11) NOT NULL';
        }
    }
}
