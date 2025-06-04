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

use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\Exception\ProviderNotFoundException;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;

interface PaymentManagerInterface
{
    /**
     * Get a payment provider by name
     *
     *
     *
     * @throws ProviderNotFoundException
     */
    public function getProvider(string $name): PaymentInterface;

    /**
     * Get configured payment providers
     *
     */
    public function getProviderTypes(): array;
}
