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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\ElasticSearch;

use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterMultiSelectFromMultiSelect;

/**
 * @deprecated This class will be moved to the SearchIndex namespace in version 2.0.0.
 */
class MultiSelectFromMultiSelect extends \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\MultiSelectFromMultiSelect
{
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        $useAndCondition = false;

        if (!$filterDefinition instanceof FilterMultiSelectFromMultiSelect) {
            throw new InvalidConfigException('invalid configuration');
        }

        $useAndCondition = $filterDefinition->getUseAndCondition();

        $field = $this->getField($filterDefinition);
        $productList->prepareGroupByValues($field, true, !$useAndCondition);
    }

    /**
     *
     * @return string[]
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);
        $preSelect = $this->getPreSelect($filterDefinition);

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if (empty($value) && !$isReload) {
            if (is_array($preSelect)) {
                $value = $preSelect;
            } else {
                $value = explode(',', $preSelect);
            }

            foreach ($value as $key => $v) {
                if (!$v) {
                    unset($value[$key]);
                }
            }
        } elseif (!empty($value) && in_array(AbstractFilterType::EMPTY_STRING, $value)) {
            $value = null;
        }

        $currentFilter[$field] = $value;

        if (!empty($value)) {
            if (!$filterDefinition instanceof FilterMultiSelectFromMultiSelect) {
                throw new InvalidConfigException('invalid configuration');
            }

            if ($filterDefinition->getUseAndCondition()) {
                foreach ($value as $entry) {
                    $productList->addCondition(['term' => ['attributes.' . $field => $entry]], $field);
                }
            } else {
                $boolArray = [];
                foreach ($value as $entry) {
                    $boolArray[] = ['term' => ['attributes.' . $field => $entry]];
                }

                $productList->addCondition(['bool' => ['should' => $boolArray, 'minimum_should_match' => 1]], $field);
            }
        }

        return $currentFilter;
    }
}
