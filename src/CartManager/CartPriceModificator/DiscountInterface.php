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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator;

use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;

/**
 * Special interface for price modifications added by discount pricing rules for carts
 */
interface DiscountInterface extends CartPriceModificatorInterface
{
    public function setAmount(Decimal $amount): DiscountInterface;

    public function getAmount(): Decimal;
}
