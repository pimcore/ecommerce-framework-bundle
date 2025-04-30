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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Dao;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Logger;
use Pimcore\Model\Exception\NotFoundException;

/**
 * @method Dao getDao()
 */
class CartCheckoutData extends AbstractCartCheckoutData
{
    public function save(): void
    {
        $this->getDao()->save();
    }

    public static function getByKeyCartId(string $key, int|string $cartId): ?AbstractCartCheckoutData
    {
        $cacheKey = CartCheckoutData\Dao::TABLE_NAME . '_' . $key . '_' . $cartId;

        try {
            $checkoutDataItem = RuntimeCache::get($cacheKey);
        } catch (\Exception $e) {
            try {
                $checkoutDataItem = new self();
                $checkoutDataItem->getDao()->getByKeyCartId($key, $cartId);
                RuntimeCache::set($cacheKey, $checkoutDataItem);
            } catch (NotFoundException $ex) {
                Logger::debug($ex->getMessage());

                return null;
            }
        }

        return $checkoutDataItem;
    }

    public static function removeAllFromCart(int|string $cartId): void
    {
        $checkoutDataItem = new self();
        $checkoutDataItem->getDao()->removeAllFromCart($cartId);
    }
}
