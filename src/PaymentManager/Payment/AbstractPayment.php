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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;
use Pimcore\Model\DataObject\Listing\Concrete;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPayment implements PaymentInterface
{
    protected bool $recurringPaymentEnabled = false;

    protected string $configurationKey;

    protected function processOptions(array $options): void
    {
        if (isset($options['recurring_payment_enabled'])) {
            $this->recurringPaymentEnabled = (bool) $options['recurring_payment_enabled'];
        }
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setDefined('recurring_payment_enabled')
            ->setAllowedTypes('recurring_payment_enabled', ['bool']);

        return $resolver;
    }

    public function isRecurringPaymentEnabled(): bool
    {
        return $this->recurringPaymentEnabled;
    }

    public function setRecurringPaymentSourceOrderData(AbstractOrder $sourceOrder, object $paymentBrick): void
    {
        throw new \RuntimeException('setRecurringPaymentSourceOrderData not implemented for ' . get_class($this));
    }

    public function applyRecurringPaymentCondition(Concrete $orderListing, array $additionalParameters = []): void
    {
        throw new \RuntimeException('applyRecurringPaymentCondition not implemented for ' . get_class($this));
    }

    public function getConfigurationKey(): string
    {
        return $this->configurationKey;
    }

    public function setConfigurationKey(string $configurationKey): void
    {
        $this->configurationKey = $configurationKey;
    }
}
