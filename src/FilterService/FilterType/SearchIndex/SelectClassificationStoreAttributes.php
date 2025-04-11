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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\SearchIndex;

use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

class SelectClassificationStoreAttributes extends AbstractFilterType
{
    /**
     * extract list of excluded keys from filter definition
     *
     *
     */
    protected function extractExcludedKeys(AbstractFilterDefinitionType $filterDefinition): array
    {
        $excludedKeys = [];

        if (method_exists($filterDefinition, 'getExcludedKeyIds') && $filterDefinition->getExcludedKeyIds()) {
            $excludedKeys = explode(',', $filterDefinition->getExcludedKeyIds());
            $excludedKeys = array_map('intval', $excludedKeys);
        }

        return $excludedKeys;
    }

    protected function sortResult(AbstractFilterDefinitionType $filterDefinition, array $keyCollection): array
    {
        if (!method_exists($filterDefinition, 'getKeyIdPriorityOrder') || empty($filterDefinition->getKeyIdPriorityOrder())) {
            return $keyCollection;
        }

        $priorityKeys = explode(',', $filterDefinition->getKeyIdPriorityOrder());
        $priorityKeys = array_map('intval', $priorityKeys);

        $sortedCollection = [];

        foreach ($priorityKeys as $key) {
            $sortedCollection[$key] = $keyCollection[$key];
            unset($keyCollection[$key]);
        }

        return $sortedCollection + $keyCollection;
    }

    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        $field = $this->getField($filterDefinition);
        $keysField = $field . '.keys';
        $valuesField = $field . '.values';

        $productList->prepareGroupByValues($keysField, false, true);
        $values = $productList->getGroupByValues($keysField, false, false);

        $excludedKeys = $this->extractExcludedKeys($filterDefinition);
        foreach ($values as $keyId) {
            if (in_array($keyId, $excludedKeys)) {
                continue;
            }

            $subField = $valuesField . '.' . $keyId . '.keyword';
            $productList->prepareGroupByValues($subField, false, true);
        }
    }

    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);
        $keysField = $field . '.keys';

        $keys = $productList->getGroupByValues($keysField, false, false);

        $keyCollection = [];

        $excludedKeys = $this->extractExcludedKeys($filterDefinition);
        foreach ($keys as $keyId) {
            if (in_array($keyId, $excludedKeys)) {
                continue;
            }

            $valuesField = $field . '.values.' . $keyId . '.keyword';

            $keyValues = $productList->getGroupByValues($valuesField, true, true);
            if (!empty($keyValues)) {
                $key = KeyConfig::getById($keyId);

                $keyCollection[$keyId] = [
                    'keyConfig' => $key,
                    'values' => $keyValues,
                ];
            }
        }

        $keyCollection = $this->sortResult($filterDefinition, $keyCollection);

        return [
            'label' => $filterDefinition->getLabel(),
            'fieldname' => $field,
            'currentValue' => $currentFilter[$field] ?? null,
            'values' => $keyCollection,
            'metaData' => $filterDefinition->getMetaData(),
        ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $nestedPath = $field . '.values';

        $value = $params[$field] ?? null;

        if (is_array($value)) {
            foreach ($value as $keyId => $keyValue) {
                $filterValue = trim($keyValue);
                if ($filterValue == AbstractFilterType::EMPTY_STRING) {
                    $filterValue = null;
                }

                if ($filterValue) {
                    $currentFilter[$field][$keyId] = $filterValue;

                    $valueField = $nestedPath . '.' . $keyId . '.keyword';
                    $productList->addCondition($filterValue, $this->getConditionField($valueField, $isPrecondition));
                }
            }
        }

        return $currentFilter;
    }
}
