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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\V7;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use Pimcore\Model\DataObject\Folder;

interface OrderManagerInterface
{
    public function createOrderList(): OrderListInterface;

    public function createOrderAgent(AbstractOrder $order): OrderAgentInterface;

    public function setParentOrderFolder(int|Folder $orderParentFolder): void;

    public function setOrderClass(string $classname): void;

    public function setOrderItemClass(string $classname): void;

    /**
     * Looks if order object for given cart already exists, otherwise creates it
     *
     */
    public function getOrCreateOrderFromCart(CartInterface $cart): AbstractOrder;

    public function recreateOrder(CartInterface $cart): AbstractOrder;

    public function recreateOrderBasedOnSourceOrder(AbstractOrder $sourceOrder): AbstractOrder;

    /**
     * Looks if order object for given cart exists and returns it - it does not create it!
     *
     *
     */
    public function getOrderFromCart(CartInterface $cart): ?AbstractOrder;

    /**
     * Returns order based on given payment status
     *
     *
     */
    public function getOrderByPaymentStatus(StatusInterface $paymentStatus): ?AbstractOrder;

    /**
     * Builds order listing
     *
     *
     * @throws \Exception
     */
    public function buildOrderList(): \Pimcore\Model\DataObject\Listing\Concrete;

    /**
     * Build order item listing
     *
     *
     * @throws \Exception
     */
    public function buildOrderItemList(): \Pimcore\Model\DataObject\Listing\Concrete;

    public function cartHasPendingPayments(CartInterface $cart): bool;

    /**
     *
     *
     * @throws UnsupportedException
     */
    public function orderNeedsUpdate(CartInterface $cart, AbstractOrder $order): bool;
}
