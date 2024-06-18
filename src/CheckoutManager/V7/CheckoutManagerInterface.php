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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutStepInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\HandlePendingPayments\HandlePendingPaymentsStrategyInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractPaymentInformation;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentRequest\AbstractRequest;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentResponse\StartPaymentResponseInterface;

interface CheckoutManagerInterface
{
    /**
     * Returns all checkout steps defined for this checkout
     *
     * @return CheckoutStepInterface[]
     */
    public function getCheckoutSteps(): array;

    /**
     * Returns checkout step with given name
     *
     *
     */
    public function getCheckoutStep(string $stepName): ?CheckoutStepInterface;

    /**
     * Returns current checkout step
     *
     */
    public function getCurrentStep(): CheckoutStepInterface;

    /**
     * Returns the cart the checkout is started with
     *
     */
    public function getCart(): CartInterface;

    /**
     * Commits checkout step
     *
     * All previous steps must be committed, otherwise committing step is not allowed
     *
     *
     */
    public function commitStep(CheckoutStepInterface $step, mixed $data): bool;

    /**
     * Checks if checkout is finished (= all checkout steps are committed)
     * only a finished checkout can be committed
     *
     */
    public function isFinished(): bool;

    /**
     * Returns if there currently is an active payment (init or pending)
     *
     */
    public function hasActivePayment(): bool;

    /**
     * Init payment for checkout - only possible if payment provider is configured
     * creates PaymentInformation with init state, does not change order state
     *
     *
     * @throws UnsupportedException
     */
    public function initOrderPayment(): AbstractPaymentInformation;

    /**
     * Starts payment for checkout and also starts payment provider
     * - only possible if payment provider is configured
     *
     *
     *
     * @throws UnsupportedException
     */
    public function startOrderPaymentWithPaymentProvider(AbstractRequest $paymentConfig): StartPaymentResponseInterface;

    public function setHandlePendingPaymentsStrategy(HandlePendingPaymentsStrategyInterface $handlePendingPaymentsStrategy): void;

    /**
     * Cancels payment for current payment info
     *
     *  - payment will be cancelled, order state will be resetted and cart will we writable again.
     *
     * -> this should be used, when user cancels payment
     *
     * Only possible when payment state is PENDING, otherwise exception is thrown
     *
     *
     * @throws UnsupportedException
     */
    public function cancelStartedOrderPayment(): ?AbstractOrder;

    /**
     * Returns order (creates it if not available yet)
     *
     */
    public function getOrder(): AbstractOrder;

    /**
     * Facade method for
     *
     *  - handling payment response and
     *  - commit order payment
     *
     * Always handles payment and updates payment information - even if order is already committed or checkout is not
     * finished (anymore)
     *
     * Use this for committing order when payment is activated
     *
     * Delegates to commit order processor
     *
     *
     */
    public function handlePaymentResponseAndCommitOrderPayment(StatusInterface|array $paymentResponseParams): AbstractOrder;

    /**
     * Start and commits payment based on a previously performed payment
     * provided via the source order.
     *
     * @param string $customerId             Only allow recurring payment to be performed on source-orders of the same user
     *
     */
    public function startAndCommitRecurringOrderPayment(AbstractOrder $sourceOrder, string $customerId): AbstractOrder;

    /**
     * Commits order - does not consider any payment
     *
     * Use this for committing order when no payment is activated
     *
     * Delegates to commit order processor
     *
     */
    public function commitOrder(): AbstractOrder;

    /**
     * Returns if checkout process and subsequently order is committed
     * basically checks, if order is available and if this order is committed
     *
     */
    public function isCommitted(): bool;

    /**
     * Returns payment adapter
     *
     */
    public function getPayment(): ?PaymentInterface;

    /**
     * Cleans up orders with state pending payment after 1h -> delegates this to commit order processor
     *
     */
    public function cleanUpPendingOrders(): void;
}
