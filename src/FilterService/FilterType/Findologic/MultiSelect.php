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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\Findologic;

use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterMultiSelect;

class MultiSelect extends \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\MultiSelect
{
    /**
     * @param FilterMultiSelect $filterDefinition
     *
     * @throws \Exception
     */
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);

        $values = [];
        foreach ($productList->getGroupByValues($this->getField($filterDefinition), true) as $value) {
            $values[] = ['value' => $value['label'],
                'count' => $value['count'], ];
        }

        // add current filter. workaround for findologic behavior
        if (array_key_exists($field, $currentFilter) && $currentFilter[$field] != null) {
            foreach ($currentFilter[$field] as $value) {
                $add = true;
                foreach ($values as $v) {
                    if ($v['value'] == $value) {
                        $add = false;

                        break;
                    }
                }

                if ($add) {
                    array_unshift($values, [
                        'value' => $value, 'label' => $value, 'count' => null, 'parameter' => null,
                    ]);
                }
            }
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentFilter[$field],
            'values' => $values,
            'fieldname' => $field,
            'resultCount' => $productList->count(),
        ];
    }

    /**
     * @param FilterMultiSelect $filterDefinition
     *
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        // init
        $field = $this->getField($filterDefinition);
        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        // set defaults
        if (empty($value) && !$isReload && ($preSelect = $this->getPreSelect($filterDefinition))) {
            $value = explode(',', $preSelect);
        }

        if (!empty($value) && in_array(AbstractFilterType::EMPTY_STRING, $value)) {
            $value = [];
        }

        $currentFilter[$field] = $value;

        if ($value) {
            $productList->addCondition($value, $field);
        }

        return $currentFilter;
    }
}
