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

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartItemInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\ProductInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Service;

class DefaultService implements ServiceInterface
{
    protected string $offerClass;

    protected string $offerItemClass;

    protected string|false $parentFolderPath;

    protected Folder $parentFolder;

    public function __construct(string $offerClass, string $offerItemClass, string $parentFolderPath)
    {
        if (!class_exists($offerClass)) {
            throw new \InvalidArgumentException(sprintf('Offer class "%s" does not exist.', $offerClass));
        }

        if (!class_exists($offerItemClass)) {
            throw new \InvalidArgumentException(sprintf('Offer item class "%s" does not exist.', $offerItemClass));
        }

        $this->offerClass = $offerClass;
        $this->offerItemClass = $offerItemClass;

        $maybeStrftime = str_contains($parentFolderPath, '%');
        if (substr_count($parentFolderPath, '*') % 2 === 0 && !$maybeStrftime) {
            $pattern = '/\*([^\*]+)\*/';
            $offerParentPath = preg_replace_callback($pattern, function ($matches) {
                return CarbonImmutable::now()->isoFormat($matches[1]);
            }, $parentFolderPath);
        } else {
            trigger_deprecation(
                'pimcore/ecommerce-framework-bundle',
                '1.1',
                'Please use `offer_parent_path` instead of `offer_tool:order_storage:parent_folder_path`, as strftime() is deprecated.'
            );
            $offerParentPath = strftime($parentFolderPath, time());
        }

        $this->parentFolderPath = $offerParentPath;
    }

    protected function getParentFolder(): Folder
    {
        $folder = Folder::getByPath($this->parentFolderPath);
        if (!$folder) {
            $folder = Service::createFolderByPath($this->parentFolderPath);
        }

        if (!$folder) {
            throw new \RuntimeException(sprintf(
                'Unable to create/load parent folder from path "%s"',
                $this->parentFolderPath
            ));
        }

        return $folder;
    }

    /**
     * @param CartItemInterface[] $excludeItems
     *
     */
    public function createNewOfferFromCart(CartInterface $cart, array $excludeItems = []): AbstractOffer
    {
        $tempOfferNumber = uniqid('offer_');
        $offer = $this->getNewOfferObject($tempOfferNumber);
        $offer->setOfferNumber($tempOfferNumber);
        $offer->setTotalPrice($cart->getPriceCalculator()->getGrandTotal()->getAmount()->asString());
        $offer->setCartId($cart->getId());
        $offer->save();

        $excludedItemKeys = $this->getExcludedItemKeys($excludeItems);

        $offerItems = [];
        $i = 0;
        foreach ($cart->getItems() as $item) {
            $i++;

            if (!$excludedItemKeys[$item->getItemKey()]) {
                $offerItem = $this->createOfferItem($item, $offer);
                $offerItem->save();

                $offerItems[] = $offerItem;
            }
        }

        $offer->setItems($offerItems);
        $offer->save();

        return $offer;
    }

    protected function getExcludedItemKeys(array $excludeItems): array
    {
        $excludedItemKeys = [];
        foreach ($excludeItems as $item) {
            $excludedItemKeys[$item->getItemKey()] = $item->getItemKey();
        }

        return $excludedItemKeys;
    }

    protected function getNewOfferObject(string $tempOfferNumber): AbstractOffer
    {
        /** @var AbstractOffer $offer */
        $offer = new $this->offerClass();
        $offer->setParent($this->getParentFolder());
        $offer->setCreationDate(time());
        $offer->setKey($tempOfferNumber);
        $offer->setPublished(true);
        $offer->setDateCreated(new Carbon());

        return $offer;
    }

    public function getNewOfferItemObject(): AbstractOfferItem
    {
        return new $this->offerItemClass();
    }

    protected function createOfferItem(CartItemInterface $item, AbstractObject $parent): AbstractOfferItem
    {
        $offerItem = $this->getNewOfferItemObject();
        $offerItem->setParent($parent);
        $offerItem->setPublished(true);
        $offerItem->setCartItemKey($item->getItemKey());
        $offerItem->setKey($item->getProduct()->getId() . '_' . $item->getItemKey());

        $product = $item->getProduct();
        $offerItem->setAmount($item->getCount());
        $offerItem->setProduct($product);
        if ($product instanceof CheckoutableInterface) {
            $offerItem->setProductName($product->getOSName());
            $offerItem->setProductNumber($product->getOSProductNumber());
        }

        $offerItem->setComment($item->getComment());

        $price = $item->getTotalPrice()->getAmount();
        $price = $this->priceTransformationHook($price);

        $offerItem->setOriginalTotalPrice($price->asString());
        $offerItem->setFinalTotalPrice($price->asString());

        $offerItem->save();

        $subItems = $item->getSubItems();
        if (!empty($subItems)) {
            $offerSubItems = [];

            foreach ($subItems as $subItem) {
                $offerSubItem = $this->createOfferItem($subItem, $offerItem);
                $offerSubItem->save();
                $offerSubItems[] = $offerSubItem;
            }

            $offerItem->setSubItems($offerSubItems);
            $offerItem->save();
        }

        return $offerItem;
    }

    protected function updateOfferItem(CartItemInterface $cartItem, AbstractOfferItem $offerItem): AbstractOfferItem
    {
        $offerItem->setAmount($cartItem->getCount());
        $offerItem->setProduct($cartItem->getProduct());
        if ($offerItem->getProduct()) {
            $offerItem->setProductName($cartItem->getProduct()->getOSName());
            $offerItem->setProductNumber($cartItem->getProduct()->getOSProductNumber());
        }

        $offerItem->setComment($cartItem->getComment());

        $price = $cartItem->getTotalPrice()->getAmount();
        $price = $this->priceTransformationHook($price);

        $originalTotalPrice = Decimal::create($offerItem->getOriginalTotalPrice());
        if (!$price->equals($originalTotalPrice)) {
            $offerItem->setOriginalTotalPrice($price->asString());
            $offerItem->setFinalTotalPrice($price->asString());
        }

        //Delete all subitems and add them as new items
        $offerSubItems = $offerItem->getSubItems();
        foreach ($offerSubItems as $i) {
            $i->delete();
        }

        $subItems = $cartItem->getSubItems();
        if (!empty($subItems)) {
            $offerSubItems = [];

            foreach ($subItems as $subItem) {
                $offerSubItem = $this->createOfferItem($subItem, $offerItem);
                $offerSubItem->save();
                $offerSubItems[] = $offerSubItem;
            }

            $offerItem->setSubItems($offerSubItems);
        }

        $offerItem->save();

        return $offerItem;
    }

    /**
     * transforms price before set to the offer tool item.
     * can be used e.g. for adding vat, ...
     *
     *
     */
    protected function priceTransformationHook(Decimal $price): Decimal
    {
        return $price;
    }

    protected function setCurrentCustomer(AbstractOffer $offer): AbstractOffer
    {
        $env = Factory::getInstance()->getEnvironment();

        if (@class_exists('\Pimcore\Model\DataObject\Customer')) {
            $customer = \Pimcore\Model\DataObject\Customer::getById($env->getCurrentUserId());
            $offer->setCustomer($customer);
        }

        return $offer;
    }

    public function updateOfferFromCart(AbstractOffer $offer, CartInterface $cart, array $excludeItems = [], bool $save = true): AbstractOffer
    {
        $excludedItemKeys = $this->getExcludedItemKeys($excludeItems);

        if ($cart->getId() != $offer->getCartId()) {
            throw new \Exception('Cart does not match to the offer given, update is not possible');
        }

        //Update existing offer items
        $offerItems = $offer->getItems();
        $newOfferItems = [];
        foreach ($offerItems as $offerItem) {
            $cartItem = $cart->getItem($offerItem->getCartItemKey());
            if ($cartItem && !$excludedItemKeys[$offerItem->getCartItemKey()]) {
                $newOfferItems[$offerItem->getCartItemKey()] = $this->updateOfferItem($cartItem, $offerItem);
            }
        }

        //Add non existing cart items to offer
        $cartItems = $cart->getItems();
        foreach ($cartItems as $cartItem) {
            if (!array_key_exists($cartItem->getItemKey(), $newOfferItems) && !array_key_exists($cartItem->getItemKey(), $excludedItemKeys)) {
                $offerItem = $this->createOfferItem($cartItem, $offer);
                $newOfferItems[$offerItem->getCartItemKey()] = $offerItem;
            }
        }

        //Delete offer items which are not needed any more
        foreach ($offerItems as $offerItem) {
            if (!array_key_exists($offerItem->getCartItemKey(), $newOfferItems)) {
                $offerItem->delete();
            }
        }

        $offer->setItems($newOfferItems);

        //Update total price
        $offer = $this->updateTotalPriceOfOffer($offer);

        if ($save) {
            $offer->save();
        }

        return $offer;
    }

    public function updateTotalPriceOfOffer(AbstractOffer $offer): AbstractOffer
    {
        $totalPrice = Decimal::zero();

        foreach ($offer->getItems() as $item) {
            $totalPrice = $totalPrice->add(Decimal::create($item->getFinalTotalPrice()));
        }

        foreach ($offer->getCustomItems() as $item) {
            $totalPrice = $totalPrice->add(Decimal::create($item->getFinalTotalPrice()));
        }

        if ($offer->getDiscountType() === ServiceInterface::DISCOUNT_TYPE_PERCENT) {
            $discount = $totalPrice->toPercentage($offer->getDiscount());
        } else {
            $discount = Decimal::create($offer->getDiscount());
        }

        $offer->setTotalPriceBeforeDiscount($totalPrice->asString());
        $offer->setTotalPrice($totalPrice->sub($discount)->asString());

        return $offer;
    }

    public function getOffersForCart(CartInterface $cart): array
    {
        $offerListClass = $this->offerClass . '\Listing';
        $list = new $offerListClass();
        $list->setCondition('cartId = ?', [$cart->getId()]);

        return $list->load();
    }

    public function createCustomOfferToolItem(ProductInterface $product, AbstractOffer $offer): void
    {
    }
}
