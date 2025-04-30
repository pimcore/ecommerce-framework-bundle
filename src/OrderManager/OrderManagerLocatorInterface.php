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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;

interface OrderManagerLocatorInterface
{
    public function getOrderManager(?string $tenant = null): OrderManagerInterface;

    public function hasOrderManager(string $tenant): bool;
}
