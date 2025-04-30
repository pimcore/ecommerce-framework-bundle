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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\AvailabilitySystem;

use Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\NameServiceLocator;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class AvailabilitySystemLocator extends NameServiceLocator implements AvailabilitySystemLocatorInterface
{
    public function getAvailabilitySystem(?string $name = null): AvailabilitySystemInterface
    {
        return $this->locate($name);
    }

    public function hasAvailabilitySystem(string $name): bool
    {
        return $this->locator->has($name);
    }

    protected function buildNotFoundException(string $name): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'Availability system "%s" is not supported. Please check the configuration.',
            $name
        ));
    }
}
