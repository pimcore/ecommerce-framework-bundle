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

class Availability implements AvailabilityInterface
{
    private CheckoutableInterface $product;

    private bool $available;

    public function __construct(CheckoutableInterface $product, bool $available)
    {
        $this->product = $product;
        $this->available = $available;
    }

    public function getProduct(): CheckoutableInterface
    {
        return $this->product;
    }

    public function getAvailable(): bool
    {
        return $this->available;
    }
}
