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
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Pimcore\Model\DataObject\Fieldcollection\Data\FilterInputfield;

class Input extends \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\Input
{
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        $field = $this->getField($filterDefinition);

        if (!$filterDefinition instanceof FilterInputfield) {
            throw new InvalidConfigException('invalid config');
        }
        $preSelect = $filterDefinition->getPreSelect();

        $value = $params[$field] ?? null;
        $isReload = $params['is_reload'] ?? null;

        if ($value == AbstractFilterType::EMPTY_STRING) {
            $value = null;
        } elseif (empty($value) && !$isReload) {
            $value = $preSelect;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        $currentFilter[$field] = $value;

        if (!empty($value)) {
            $value = '.*"' . $value .  '".*';
            $productList->addCondition(
                ['regexp' => ['attributes.' . $field => $value]],
                $this->getConditionField($field, $isPrecondition)
            );
        }

        return $currentFilter;
    }
}
