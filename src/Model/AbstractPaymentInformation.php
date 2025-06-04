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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Model;

use Carbon\Carbon;

/**
 * Abstract base class for payment information field collection
 */
abstract class AbstractPaymentInformation extends \Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData
{
    abstract public function getPaymentStart(): ?Carbon;

    abstract public function setPaymentStart(?Carbon $paymentStart): static;

    abstract public function getPaymentFinish(): ?Carbon;

    abstract public function setPaymentFinish(?Carbon $paymentFinish): static;

    abstract public function getPaymentReference(): ?string;

    abstract public function setPaymentReference(?string $paymentReference): static;

    abstract public function getPaymentState(): ?string;

    abstract public function setPaymentState(?string $paymentState): static;

    abstract public function getMessage(): ?string;

    abstract public function getProviderData(): ?string;

    abstract public function setMessage(?string $message): static;

    abstract public function getInternalPaymentId(): ?string;

    abstract public function setInternalPaymentId(?string $internalPaymentId): static;
}
