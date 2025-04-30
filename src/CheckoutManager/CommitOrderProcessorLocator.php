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

use Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\CheckoutTenantAwareServiceLocator;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class CommitOrderProcessorLocator extends CheckoutTenantAwareServiceLocator implements CommitOrderProcessorLocatorInterface
{
    public function getCommitOrderProcessor(?string $tenant = null): CommitOrderProcessorInterface
    {
        return $this->locate($tenant);
    }

    public function hasCommitOrderProcessor(string $tenant): bool
    {
        return $this->locator->has($tenant);
    }

    protected function buildNotFoundException(string $tenant): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'Commit order processor for checkout manager tenant "%s" is not defined. Please check the configuration.',
            $tenant
        ));
    }
}
