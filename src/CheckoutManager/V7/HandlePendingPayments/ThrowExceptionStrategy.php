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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\HandlePendingPayments;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\PaymentNotAllowedException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;

class ThrowExceptionStrategy implements HandlePendingPaymentsStrategyInterface
{
    /**
     *
     *
     * @throws PaymentNotAllowedException
     * @throws \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException
     */
    public function handlePaymentNotAllowed(AbstractOrder $order, CartInterface $cart, OrderManagerInterface $orderManager): AbstractOrder
    {
        throw new PaymentNotAllowedException(
            'Payment not allowed since there is a payment pending. Cancel payment or recreate order.',
            $order,
            $cart,
            $orderManager->orderNeedsUpdate($cart, $order)
        );
    }
}
