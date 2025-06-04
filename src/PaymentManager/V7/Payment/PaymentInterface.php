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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment;

use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentRequest\AbstractRequest;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentResponse\StartPaymentResponseInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;

interface PaymentInterface
{
    public function getName(): string;

    /**
     * Starts payment
     */
    public function startPayment(OrderAgentInterface $orderAgent, PriceInterface $price, AbstractRequest $config): StartPaymentResponseInterface;

    /**
     * Handles response of payment provider and creates payment status object
     */
    public function handleResponse(StatusInterface|array $response): StatusInterface;

    /**
     * Returns the authorized data from payment provider
     */
    public function getAuthorizedData(): array;

    /**
     * Set authorized data from payment provider
     */
    public function setAuthorizedData(array $authorizedData): void;

    /**
     * Executes payment
     */
    public function executeDebit(?PriceInterface $price = null, ?string $reference = null): StatusInterface;

    /**
     * Executes credit
     */
    public function executeCredit(PriceInterface $price, string $reference, string $transactionId): StatusInterface;

    /**
     * returns configuration key in yml configuration file
     */
    public function getConfigurationKey(): string;
}
