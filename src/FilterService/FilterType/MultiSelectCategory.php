<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType;

use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Db;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterCategoryMultiselect;
use Pimcore\Model\Element\ElementInterface;

class MultiSelectCategory extends AbstractFilterType
{
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $rawValues = $productList->getGroupByValues($filterDefinition->getField(), true);
        $values = [];

        /** @var array<string, bool> $availableRelations */
        $availableRelations = [];
        if (!$filterDefinition instanceof FilterCategoryMultiselect) {
            throw new InvalidConfigException('invalid configuration');
        }

        if (method_exists($filterDefinition, 'getAvailableCategories') && $filterDefinition->getAvailableCategories()) {
            /** @var ElementInterface $rel */
            foreach ($filterDefinition->getAvailableCategories() as $rel) {
                $availableRelations[$rel->getId()] = true;
            }
        }

        foreach ($rawValues as $v) {
            if ($v['value']) {
                $explode = array_map('intval', explode(',', $v['value']));
                foreach ($explode as $e) {
                    if (empty($availableRelations) || ($availableRelations[$e] ?? false)) {
                        if (!empty($values[$e])) {
                            $count = $values[$e]['count'] + $v['count'];
                        } else {
                            $count = $v['count'];
                        }
                        $values[$e] = ['value' => $e, 'count' => $count];
                    }
                }
            }
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$filterDefinition->getField()],
            'values' => array_values($values),
            'fieldname' => $filterDefinition->getField(),
            'metaData' => $filterDefinition->getMetaData(),
            'resultCount' => $productList->count(),
        ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload) {
            $preSelect = false;
            if (method_exists($filterDefinition, 'getPreSelect')) {
                $preSelect = $filterDefinition->getPreSelect();
            }

            $value = $preSelect;
        }

        $currentFilter[$field] = $value;

        $conditions = [];
        if (!empty($value)) {
            $db = Db::get();
            foreach ($value as $category) {
                if (is_object($category)) {
                    $category = $category->getId();
                }

                $category = '%,' . trim((string)$category) . ',%';

                $conditions[] = $field . ' LIKE ' . $db->quote($category);
            }
        }

        if (count($conditions)) {
            $useAndCondition = false;
            if (method_exists($filterDefinition, 'getUseAndCondition')) {
                $useAndCondition = $filterDefinition->getUseAndCondition();
            }

            if ($useAndCondition) {
                $conditions = implode(' AND ', $conditions);
            } else {
                $conditions = '(' . implode(' OR ', $conditions) . ')';
            }

            $productList->addCondition($conditions, $this->getConditionField($field, $isPrecondition));
        }

        return $currentFilter;
    }
}
