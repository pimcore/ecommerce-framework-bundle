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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService;

use Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\AssortmentTenantAwareServiceLocator;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class FilterServiceLocator extends AssortmentTenantAwareServiceLocator implements FilterServiceLocatorInterface
{
    public function getFilterService(?string $tenant = null): FilterService
    {
        return $this->locate($tenant);
    }

    public function hasFilterService(string $tenant): bool
    {
        return $this->locator->has($tenant);
    }

    protected function buildNotFoundException(string $tenant): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'Filter service for assortment tenant "%s" is not registered. Please check the configuration.',
            $tenant
        ));
    }
}
