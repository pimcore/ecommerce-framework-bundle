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

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;

class AvailabilitySystem implements AvailabilitySystemInterface
{
    public function getAvailabilityInfo(CheckoutableInterface $product, int $quantityScale = 1, ?array $products = null): Availability|AvailabilityInterface
    {
        return new Availability($product, true);
    }
}
