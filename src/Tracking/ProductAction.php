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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking;

class ProductAction extends AbstractProductData
{
    protected float|int $quantity = 1;

    protected string $coupon = '';

    public function getQuantity(): float|int
    {
        return $this->quantity;
    }

    public function setQuantity(float|int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCoupon(): string
    {
        return $this->coupon;
    }

    public function setCoupon(string $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }
}
