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

class SessionCartCheckoutData extends AbstractCartCheckoutData
{
    protected string|int|null $cartId;

    public function save(): void
    {
        throw new \Exception('Not implemented, should not be needed for this cart type.');
    }

    public static function getByKeyCartId(string $key, int|string $cartId): ?AbstractCartCheckoutData
    {
        throw new \Exception('Not implemented, should not be needed for this cart type.');
    }

    public static function removeAllFromCart(int|string $cartId): void
    {
        $checkoutDataItem = new self();
        $cart = $checkoutDataItem->getCart();
        if ($cart instanceof SessionCart) {
            $cart->checkoutData = [];
        }
    }

    public function setCart(CartInterface $cart): void
    {
        $this->cart = $cart;
        $this->cartId = $cart->getId();
    }

    public function getCart(): ?CartInterface
    {
        if (empty($this->cart)) {
            $this->cart = SessionCart::getById($this->cartId);
        }

        return $this->cart;
    }

    public function getCartId(): int|string|null
    {
        return $this->cartId;
    }

    public function setCartId(int|string|null $cartId): void
    {
        $this->cartId = $cartId;
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
