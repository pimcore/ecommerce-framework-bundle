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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Exception;

class ResponseWithAbortedPaymentStateException extends AbstractEcommerceException
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
