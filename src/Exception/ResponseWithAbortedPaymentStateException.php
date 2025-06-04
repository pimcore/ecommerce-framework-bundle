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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Exception;

class ResponseWithAbortedPaymentStateException extends UnsupportedException
{
    protected ?string $paymentState;

    public function __construct(?string $newPaymentState)
    {
        $message = 'Got response although payment state was already aborted, new payment state was ' . $newPaymentState;
        parent::__construct($message);
        $this->paymentState = $newPaymentState;
    }

    public function getPaymentState(): ?string
    {
        return $this->paymentState;
    }
}
