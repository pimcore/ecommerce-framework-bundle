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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;

interface CommitOrderProcessorInterface
{
    /**
     * Checks if order is already committed and payment information with same internal payment id has same state
     *
     *
     *
     * @throws \Exception
     * @throws UnsupportedException
     */
    public function committedOrderWithSamePaymentExists(StatusInterface|array $paymentResponseParams, PaymentInterface $paymentProvider): ?AbstractOrder;

    /**
     * Facade method for
     *
     *  - handling payment response and
     *  - commit order payment
     *
     * Can be used by controllers to commit orders with payment
     *
     *
     */
    public function handlePaymentResponseAndCommitOrderPayment(StatusInterface|array $paymentResponseParams, PaymentInterface $paymentProvider): AbstractOrder;

    /**
     * Commits order payment
     *
     *  - updates order payment information in order object
     *  - only when payment status == [ORDER_STATE_COMMITTED, ORDER_STATE_PAYMENT_AUTHORIZED] -> order is committed
     *
     * Use this for committing order when payment is activated
     *
     * @param AbstractOrder|null $sourceOrder Source order for recurring payment
     *
     */
    public function commitOrderPayment(StatusInterface $paymentStatus, PaymentInterface $paymentProvider, ?AbstractOrder $sourceOrder = null): AbstractOrder;

    /**
     * Commits order
     *
     *
     */
    public function commitOrder(AbstractOrder $order): AbstractOrder;

    /**
     * Cleans up orders with state pending payment after 1h
     *
     */
    public function cleanUpPendingOrders(): void;
}
