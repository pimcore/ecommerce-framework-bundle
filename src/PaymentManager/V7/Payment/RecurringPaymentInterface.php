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

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Model\DataObject\Listing\Concrete;

interface RecurringPaymentInterface extends PaymentInterface
{
    /**
     * Payment supports recurring payment
     */
    public function isRecurringPaymentEnabled(): bool;

    public function setRecurringPaymentSourceOrderData(AbstractOrder $sourceOrder, object $paymentBrick): void;

    public function applyRecurringPaymentCondition(Concrete $orderListing, array $additionalParameters = []): void;
}
