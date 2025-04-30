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
use Pimcore\Model\Element\ElementInterface;

class CustomerObject implements OrderListFilterInterface
{
    protected ElementInterface $customer;

    public function __construct(ElementInterface $customer)
    {
        $this->customer = $customer;
    }

    public function apply(OrderListInterface $orderList): static
    {
        $orderList->addCondition('order.customer__id = ?', (string) $this->customer->getId());

        return $this;
    }
}
