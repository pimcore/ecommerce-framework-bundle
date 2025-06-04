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

/**
 * Base filter for LIKE queries. For simple queries you'll just
 * need to override the getConditionColumn() method and return
 * the query part coming before LIKE.
 */
abstract class AbstractSearch implements OrderListFilterInterface
{
    /**
     * Search value
     *
     */
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = trim($value);
    }

    /**
     * Return the string coming before LIKE, e.g. 'order.invoiceEmail'
     *
     */
    abstract protected function getConditionColumn(): string;

    /**
     * Pad the value with wildcards
     *
     */
    protected function getConditionValue(): string
    {
        return '%' . $this->value . '%';
    }

    public function apply(OrderListInterface $orderList): static
    {
        if (empty($this->value)) {
            return $this;
        }

        $this->prepareApply($orderList);

        $query = sprintf('%s LIKE ?', $this->getConditionColumn());
        $value = $this->getConditionValue();

        $orderList->addCondition($query, $value);

        return $this;
    }

    /**
     * Override if necessary (e.g. join a table)
     *
     */
    protected function prepareApply(OrderListInterface $orderList): void
    {
    }
}
