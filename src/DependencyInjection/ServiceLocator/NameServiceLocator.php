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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator;

use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Psr\Container\ContainerInterface as PsrContainerInterface;

abstract class NameServiceLocator
{
    protected PsrContainerInterface $locator;

    protected string $defaultName = 'default';

    public function __construct(PsrContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    protected function locate(?string $name = null): mixed
    {
        $name = $this->resolveName($name);

        if (!$this->locator->has($name)) {
            throw $this->buildNotFoundException($name);
        }

        return $this->locator->get($name);
    }

    protected function resolveName(?string $name = null): string
    {
        if (empty($name)) {
            return $this->defaultName;
        }

        return $name;
    }

    abstract protected function buildNotFoundException(string $name): UnsupportedException;
}
