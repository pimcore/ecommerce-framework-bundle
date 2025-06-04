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

class SessionCartItem extends AbstractCartItem implements CartItemInterface
{
    public function getCart(): CartInterface|null
    {
        if (empty($this->cart)) {
            $this->cart = SessionCart::getById($this->cartId);
        }

        return $this->cart;
    }

    public function save(): void
    {
        throw new \Exception('Not implemented, should not be needed for this cart type.');
    }

    public static function getByCartIdItemKey(int|string $cartId, string $itemKey, string $parentKey = ''): ?CartItemInterface
    {
        throw new \Exception('Not implemented, should not be needed for this cart type.');
    }

    public static function removeAllFromCart(int|string $cartId): void
    {
        $cartItem = new self();
        $cart = $cartItem->getCart();
        $cart->setItems(null);
        $cart->save();
    }

    /**
     * @return CartItemInterface[]
     */
    public function getSubItems(): array
    {
        return (array)$this->subItems;
    }

    /**
     *
     * @internal
     */
    public function __sleep(): array
    {
        $vars = parent::__sleep();

        $blockedVars = ['cart', 'product'];

        $finalVars = [];
        foreach ($vars as $key) {
            if (!in_array($key, $blockedVars)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
    }
}
