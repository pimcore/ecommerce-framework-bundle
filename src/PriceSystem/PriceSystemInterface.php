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

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\CartPriceModificatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\OnlineShopTaxClass;

interface PriceSystemInterface
{
    /**
     * Creates price info object for given product and quantity scale
     *
     * @param CheckoutableInterface&Concrete $product
     * @param int|string|null $quantityScale - Numeric or string (allowed values: PriceInfoInterface::MIN_PRICE)
     * @param CheckoutableInterface[]|null $products
     *
     */
    public function getPriceInfo(CheckoutableInterface $product, int|string $quantityScale = null, array $products = null): PriceInfoInterface;

    /**
     * Filters and orders given product IDs based on price information
     *
     *
     */
    public function filterProductIds(array $productIds, ?float $fromPrice, ?float $toPrice, string $order, int $offset, int $limit): array;

    /**
     * Returns OnlineShopTaxClass for given CheckoutableInterface
     *
     * Should be overwritten in custom price systems with suitable implementation.
     *
     *
     */
    public function getTaxClassForProduct(CheckoutableInterface $product): OnlineShopTaxClass;

    /**
     * Returns OnlineShopTaxClass for given CartPriceModificatorInterface
     *
     * Should be overwritten in custom price systems with suitable implementation.
     *
     *
     */
    public function getTaxClassForPriceModification(CartPriceModificatorInterface $modificator): OnlineShopTaxClass;
}
