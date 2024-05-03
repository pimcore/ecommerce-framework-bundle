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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;

/**
 * Interface for PriceInfo implementations of online shop framework
 */
interface PriceInfoInterface
{
    const MIN_PRICE = 'min';

    /**
     * Returns single price
     *
     */
    public function getPrice(): PriceInterface;

    /**
     * Returns total price (single price * quantity)
     *
     */
    public function getTotalPrice(): PriceInterface;

    /**
     * Returns if price is a minimal price (e.g. when having many product variants they might have a from price)
     *
     */
    public function isMinPrice(): bool;

    /**
     * Returns quantity
     *
     */
    public function getQuantity(): int|string;

    /**
     * Numeric quantity or constant PriceInterfaceInfo::MIN_PRICE
     *
     */
    public function setQuantity(int|string $quantity): void;

    /**
     * Relation to price system
     *
     *
     * @return $this
     */
    public function setPriceSystem(PriceSystemInterface $priceSystem): static;

    /**
     * Relation to product
     *
     *
     * @return $this
     */
    public function setProduct(CheckoutableInterface $product): static;

    /**
     * Returns product
     *
     */
    public function getProduct(): ?CheckoutableInterface;
}
