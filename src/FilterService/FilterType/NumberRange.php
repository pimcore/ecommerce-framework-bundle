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

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Db;

class NumberRange extends AbstractFilterType
{
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        return [
              'hideFilter' => $filterDefinition->getRequiredFilterField() && empty($currentFilter[$filterDefinition->getRequiredFilterField()]),
              'label' => $filterDefinition->getLabel(),
              'currentValue' => $currentFilter[$this->getField($filterDefinition)],
              'values' => $productList->getGroupByValues($this->getField($filterDefinition), true),
              'definition' => $filterDefinition,
              'fieldname' => $this->getField($filterDefinition),
              'metaData' => $filterDefinition->getMetaData(),
              'resultCount' => $productList->count(),
         ];
    }

    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $value = $params[$field] ?? null;

        if (empty($value)) {
            $value['from'] = method_exists($filterDefinition, 'getPreSelectFrom') ? $filterDefinition->getPreSelectFrom() : null;
            $value['to'] = method_exists($filterDefinition, 'getPreSelectTo') ? $filterDefinition->getPreSelectTo() : null;
        }

        $currentFilter[$field] = $value;

        $db = Db::get();

        $from = $value['from'] ?? null;
        $to = $value['to'] ?? null;

        if ($from && $from !== AbstractFilterType::EMPTY_STRING) {
            $productList->addCondition(
                $field . ' >= ' . $db->quote($from),
                $this->getConditionField($field, $isPrecondition)
            );
        }
        if ($to && $to !== AbstractFilterType::EMPTY_STRING) {
            $productList->addCondition(
                $field . ' <= ' . $db->quote($to),
                $this->getConditionField($field, $isPrecondition)
            );
        }

        return $currentFilter;
    }
}
