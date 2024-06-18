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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrderItem;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\ProductInterface;

interface TrackingItemBuilderInterface
{
    /**
     * Build a product view object
     *
     *
     */
    public function buildProductViewItem(ProductInterface $product): ProductAction;

    /**
     * Build a product action item object
     *
     *
     */
    public function buildProductActionItem(ProductInterface $product, int $quantity = 1): ProductAction;

    /**
     * Build a product impression object
     *
     *
     */
    public function buildProductImpressionItem(ProductInterface $product, string $list = 'default'): ProductImpression;

    /**
     * Build a checkout transaction object
     *
     *
     */
    public function buildCheckoutTransaction(AbstractOrder $order): Transaction;

    /**
     * Build checkout items
     *
     *
     * @return ProductAction[]
     */
    public function buildCheckoutItems(AbstractOrder $order): array;

    /**
     * Build checkout items by cart
     *
     *
     * @return ProductAction[]
     */
    public function buildCheckoutItemsByCart(CartInterface $cart): array;

    /**
     * Build a checkout item object
     *
     *
     */
    public function buildCheckoutItem(AbstractOrder $order, AbstractOrderItem $orderItem): ProductAction;
}
