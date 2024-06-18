<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OfferTool;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface;

interface ServiceInterface
{
    const DISCOUNT_TYPE_PERCENT = 'percent';

    const DISCOUNT_TYPE_AMOUNT = 'amount';

    /**
     * @param CartItemInterface[] $excludeItems
     *
     */
    public function createNewOfferFromCart(CartInterface $cart, array $excludeItems = []): AbstractOffer;

    public function updateOfferFromCart(AbstractOffer $offer, CartInterface $cart, array $excludeItems = []): AbstractOffer;

    public function updateTotalPriceOfOffer(AbstractOffer $offer): AbstractOffer;

    /**
     *
     * @return AbstractOffer[]
     */
    public function getOffersForCart(CartInterface $cart): array;

    public function getNewOfferItemObject(): AbstractOfferItem;
}
