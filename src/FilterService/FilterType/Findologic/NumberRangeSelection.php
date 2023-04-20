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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\Findologic;

use Pimcore\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterNumberRangeSelection;

class NumberRangeSelection extends \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\NumberRangeSelection
{
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        //$productList->prepareGroupByValues($this->getField($filterDefinition), true);
    }

    /**
     * @param FilterNumberRangeSelection $filterDefinition
     * @param ProductListInterface $productList
     * @param array $currentFilter
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        $field = $this->getField($filterDefinition);
        $ranges = $filterDefinition->getRanges();

        $groupByValues = $productList->getGroupByValues($field, true);

        $counts = [];
        foreach ($ranges->getData() as $row) {
            $counts[$row['from'] . '_' . $row['to']] = 0;
        }

        foreach ($groupByValues as $groupByValue) {
            if ($groupByValue['label']) {
                $value = (float)$groupByValue['label'];

                if (!$value) {
                    $value = 0;
                }
                foreach ($ranges->getData() as $row) {
                    if ((empty($row['from']) || ($row['from'] <= $value)) && (empty($row['to']) || $row['to'] >= $value)) {
                        $counts[$row['from'] . '_' . $row['to']] += $groupByValue['count'];

                        break;
                    }
                }
            }
        }
        $values = [];
        foreach ($ranges->getData() as $row) {
            if ($counts[$row['from'] . '_' . $row['to']]) {
                $values[] = ['from' => $row['from'], 'to' => $row['to'], 'label' => $this->createLabel($row), 'count' => $counts[$row['from'] . '_' . $row['to']], 'unit' => $filterDefinition->getUnit()];
            }
        }

        $currentValue = '';
        if ($currentFilter[$field]['from'] || $currentFilter[$field]['to']) {
            $currentValue = implode('-', $currentFilter[$field]);
        }

        return [
            'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
            'label' => $filterDefinition->getLabel(),
            'currentValue' => $currentValue,
            'currentNiceValue' => $this->createLabel($currentFilter[$field]),
            'unit' => $filterDefinition->getUnit(),
            'values' => $values,
            'definition' => $filterDefinition,
            'fieldname' => $field,
            'resultCount' => $productList->count(),
        ];
    }

    /**
     * @param FilterNumberRangeSelection $filterDefinition
     * @param ProductListInterface $productList
     * @param array $currentFilter
     * @param array $params
     * @param bool $isPrecondition
     *
     * @return array
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $rawValue = $params[$field] ?? null;

        if (!empty($rawValue) && $rawValue != AbstractFilterType::EMPTY_STRING) {
            $values = explode('-', $rawValue);
            $value['from'] = trim($values[0]);
            $value['to'] = trim($values[1]);
        } elseif ($rawValue == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } else {
            $value = ['from' => null, 'to' => null];
            if (method_exists($filterDefinition, 'getPreSelectFrom')) {
                $value['from'] = $filterDefinition->getPreSelectFrom();
            }
            if (method_exists($filterDefinition, 'getPreSelectTo')) {
                $value['to'] = $filterDefinition->getPreSelectTo();
            }
        }

        $currentFilter[$field] = $value;

        if ($value['from'] || $value['to']) {
            $v = [];
            if ($value['from']) {
                $v['min'] = $value['from'];
            } else {
                $v['min'] = 0;
            }

            if ($value['to']) {
                $v['max'] = $value['to'];
            } else {
                $v['max'] = 9999999999999999;       // findologic won't accept only one of max or min, always needs both
            }
            $productList->addCondition($v, $field);
        }

        return $currentFilter;
    }

    private function createLabel(mixed $data): string
    {
        if (is_array($data)) {
            if (!empty($data['from'])) {
                if (!empty($data['to'])) {
                    return $data['from'] . ' - ' . $data['to'];
                } else {
                    return $this->translator->trans('more than') . ' ' . $data['from'];
                }
            } elseif (!empty($data['to'])) {
                return $this->translator->trans('less than') . ' ' . $data['to'];
            }
        }

        return '';
    }
}
