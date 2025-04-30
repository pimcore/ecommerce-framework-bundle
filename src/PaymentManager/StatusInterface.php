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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager;

/**
 * Interface StatusInterface
 */
interface StatusInterface
{
    const STATUS_PENDING = 'paymentPending';

    const STATUS_AUTHORIZED = 'paymentAuthorized';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_CLEARED = 'committed';

    /**
     * payment reference from payment provider
     *
     */
    public function getPaymentReference(): string;

    /**
     * pimcore internal payment id, necessary to identify payment information in order object
     *
     */
    public function getInternalPaymentId(): string;

    /**
     * payment message provided from payment provider - e.g. error message on error
     *
     */
    public function getMessage(): string;

    /**
     * internal pimcore order status - see also constants \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder::ORDER_STATE_*
     *
     */
    public function getStatus(): string;

    /**
     * additional payment data
     *
     */
    public function getData(): array;
}
