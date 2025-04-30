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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter;

use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListFilterInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;

class ProductType implements OrderListFilterInterface
{
    protected array $types = [];

    public function apply(OrderListInterface $orderList): static
    {
        $types = [];
        $orderList->joinOrderItemObjects();

        $db = \Pimcore\Db::get();
        foreach ($this->getTypes() as $type) {
            $types[] = $db->quote($type);
        }
        $queryBuilder = $orderList->getQueryBuilder();
        $condition = 'orderItemObjects.className IN (' . implode(',', $types) . ')';
        $queryBuilder->andWhere($condition);

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): static
    {
        $this->types = $types;

        return $this;
    }
}
