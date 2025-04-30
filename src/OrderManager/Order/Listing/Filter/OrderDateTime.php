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

class OrderDateTime implements OrderListFilterInterface
{
    protected ?\DateTime $from = null;

    protected ?\DateTime $till = null;

    protected string $column = 'order.orderDate';

    public function apply(OrderListInterface $orderList): static
    {
        // init
        $queryBuilder = $orderList->getQueryBuilder();

        if ($this->getFrom()) {
            $queryBuilder->andWhere($this->getColumn() . ' >= :from_date')->setParameter('from_date', $this->getFrom()->getTimestamp());
        }

        if ($this->getTill()) {
            $queryBuilder->andWhere($this->getColumn() . ' <= :till_date')->setParameter('till_date', $this->getTill()->getTimestamp());
        }

        return $this;
    }

    public function getFrom(): ?\DateTime
    {
        return $this->from;
    }

    public function setFrom(\DateTime $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function getTill(): ?\DateTime
    {
        return $this->till;
    }

    public function setTill(\DateTime $till): static
    {
        $this->till = $till;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): void
    {
        $this->column = $column;
    }
}
