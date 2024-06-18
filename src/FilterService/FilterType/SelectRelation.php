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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Db;
use Pimcore\Logger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterRelation;
use Pimcore\Model\DataObject\Folder;

class SelectRelation extends AbstractFilterType
{
    /**
     * @param FilterRelation $filterDefinition
     *
     * @throws \Exception
     */
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);

        $values = $productList->getGroupByRelationValues($field, true);

        $objects = [];
        Logger::info('Load Objects...');

        $availableRelations = [];
        if ($filterDefinition->getAvailableRelations()) {
            $availableRelations = $this->loadAllAvailableRelations($filterDefinition->getAvailableRelations());
        }

        foreach ($values as $v) {
            if (empty($availableRelations) || ($availableRelations[$v['value']] ?? false)) {
                $objects[$v['value']] = DataObject::getById($v['value']);
            }
        }
        Logger::info('done.');

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$field],
            'values' => $values,
            'objects' => $objects,
            'fieldname' => $field,
            'metaData' => $filterDefinition->getMetaData(),
            'resultCount' => $productList->count(),
        ];
    }

    /**
     * @param DataObject\AbstractObject[] $availableRelations
     * @param array<int, true> $availableRelationsArray
     *
     * @return array<int, true>
     */
    protected function loadAllAvailableRelations(array $availableRelations, array $availableRelationsArray = []): array
    {
        foreach ($availableRelations as $rel) {
            if ($rel instanceof Folder) {
                $availableRelationsArray = $this->loadAllAvailableRelations($rel->getChildren()->load(), $availableRelationsArray);
            } else {
                $availableRelationsArray[$rel->getId()] = true;
            }
        }

        return $availableRelationsArray;
    }

    /**
     * @param FilterRelation $filterDefinition
     *
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $preSelect = $this->getPreSelect($filterDefinition);

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if (empty($value) && !$isReload) {
            $o = $preSelect;
            if (!empty($o)) {
                $value = $o;
            }
        } elseif ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        }

        $currentFilter[$field] = $value;

        $db = Db::get();
        if (!empty($value)) {
            $productList->addRelationCondition($field, 'dest = ' . $db->quote($value));
        }

        return $currentFilter;
    }
}
