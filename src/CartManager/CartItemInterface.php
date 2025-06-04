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

use Pimcore\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilityInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractSetProductEntry;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;

/**
 * Interface for cart item implementations of online shop framework
 */
interface CartItemInterface
{
    public function getProduct(): CheckoutableInterface;

    public function getCount(): int;

    public function getItemKey(): string;

    public function setProduct(CheckoutableInterface $product): void;

    public function setCount(int $count): void;

    public function setCart(CartInterface $cart): void;

    public function getCart(): ?CartInterface;

    /**
     * @return CartItemInterface[]
     */
    public function getSubItems(): array;

    /**
     * @param CartItemInterface[] $subItems
     *
     */
    public function setSubItems(array $subItems): void;

    public function getPrice(): PriceInterface;

    public function getTotalPrice(): PriceInterface;

    public function getPriceInfo(): PriceInfoInterface;

    public function setComment(string $comment): void;

    public function getComment(): string;

    /**
     * @return AbstractSetProductEntry[]
     */
    public function getSetEntries(): array;

    public function getAvailabilityInfo(): AvailabilityInterface;

    /**
     * @static
     *
     *
     */
    public static function getByCartIdItemKey(int|string $cartId, string $itemKey, string $parentKey = ''): ?CartItemInterface;

    /**
     * @static
     *
     */
    public static function removeAllFromCart(int|string $cartId): void;

    public function save(): void;

    public function setAddedDate(?\DateTime $date = null): void;

    public function getAddedDate(): \DateTime;

    /**
     * @return int unix timestamp
     */
    public function getAddedDateTimestamp(): int;

    public function setAddedDateTimestamp(int $time): void;

    /**
     * get item name
     *
     */
    public function getName(): string;

    public function getCustomProperties(): array;

    public function setCustomProperties(array $customProperties): void;
}
