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

use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\TenantConfigInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterMultiSelect;

class MultiSelect extends \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\MultiSelect
{
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        if (!$filterDefinition instanceof FilterMultiSelect) {
            throw new InvalidConfigException('invalid configuration');
        }

        $field = $this->getField($filterDefinition);
        $productList->prepareGroupByValues($field, true, !$filterDefinition->getUseAndCondition());
    }

    /**
     * @param FilterMultiSelect $filterDefinition
     *
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $preSelect = $this->getPreSelect($filterDefinition);

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if (!empty($value)) {
            if (!is_array($value)) {
                $value = [$value];
            }
        }

        if (empty($value) && !$isReload) {
            if (!empty($preSelect) || $preSelect == '0') {
                $value = explode(',', $preSelect);
            }
        } elseif (!empty($value) && in_array(AbstractFilterType::EMPTY_STRING, $value)) {
            $value = null;
        }

        $currentFilter[$field] = $value;

        if (!empty($value)) {
            $quotedValues = [];
            foreach ($value as $v) {
                if (!empty($v)) {
                    $quotedValues[] = $v;
                }
            }

            if (!$productList instanceof TenantConfigInterface) {
                throw new InvalidConfigException('invalid configuration');
            }

            $tenantConfig = $productList->getTenantConfig();
            $attributeConfig = $tenantConfig->getAttributeConfig()[$field];
            if ($attributeConfig['type'] === 'boolean') {
                foreach ($quotedValues as $k => $v) {
                    $quotedValues[$k] = (bool)$v;
                }
            }

            if (!empty($quotedValues)) {
                if ($filterDefinition->getUseAndCondition()) {
                    foreach ($quotedValues as $value) {
                        $productList->addCondition($value, $this->getConditionField($field, $isPrecondition));
                    }
                } else {
                    $productList->addCondition(
                        ['terms' => ['attributes.' . $field => $quotedValues]],
                        $this->getConditionField($field, $isPrecondition)
                    );
                }
            }
        }

        return $currentFilter;
    }
}
