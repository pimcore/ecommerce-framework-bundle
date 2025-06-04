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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;
use Pimcore\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class OrderManagerEvent extends Event
{
    use ArgumentsAwareTrait;

    protected CartInterface $cart;

    protected ?AbstractOrder $order = null;

    protected OrderManagerInterface $orderManager;

    /**
     * OrderManagerEvent constructor.
     *
     */
    public function __construct(CartInterface $cart, ?AbstractOrder $order, OrderManagerInterface $orderManager, array $arguments = [])
    {
        $this->cart = $cart;
        $this->order = $order;
        $this->orderManager = $orderManager;
        $this->arguments = $arguments;
    }

    public function getCart(): CartInterface
    {
        return $this->cart;
    }

    public function setCart(CartInterface $cart): void
    {
        $this->cart = $cart;
    }

    public function getOrder(): ?AbstractOrder
    {
        return $this->order;
    }

    public function setOrder(AbstractOrder $order): void
    {
        $this->order = $order;
    }

    public function getOrderManager(): OrderManagerInterface
    {
        return $this->orderManager;
    }

    public function setOrderManager(OrderManagerInterface $orderManager): void
    {
        $this->orderManager = $orderManager;
    }
}
