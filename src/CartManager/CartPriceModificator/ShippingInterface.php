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
 * special interface for shipping price modifications - needed for pricing rule that remove shipping costs
 */
interface ShippingInterface extends CartPriceModificatorInterface
{
    public function setCharge(Decimal $charge): CartPriceModificatorInterface;

    public function getCharge(): Decimal;
}
