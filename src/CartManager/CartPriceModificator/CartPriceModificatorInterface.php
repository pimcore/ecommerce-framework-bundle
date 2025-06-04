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

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\ModificatedPriceInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;

interface CartPriceModificatorInterface
{
    public function getName(): string;

    /**
     * function which modifies the current sub total price
     *
     * @param PriceInterface $currentSubTotal - current sub total which is modified and returned
     * @param CartInterface $cart - cart
     *
     */
    public function modify(PriceInterface $currentSubTotal, CartInterface $cart): ModificatedPriceInterface;
}
