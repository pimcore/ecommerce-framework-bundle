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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Model;

use Pimcore\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilityInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;

/**
 * Interface CheckoutableInterface
 */
interface CheckoutableInterface extends ProductInterface
{
    /**
     * defines the name of the price system for this product.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes
     */
    public function getPriceSystemName(): string;

    /**
     * defines the name of the availability system for this product.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes
     */
    public function getAvailabilitySystemName(): string;

    /**
     * checks if product is bookable
     */
    public function getOSIsBookable(int $quantityScale = 1): bool;

    /**
     * returns instance of price system implementation based on result of getPriceSystemName()
     */
    public function getPriceSystemImplementation(): PriceSystemInterface;

    /**
     * returns instance of availability system implementation based on result of getAvailabilitySystemName()
     */
    public function getAvailabilitySystemImplementation(): AvailabilitySystemInterface;

    /**
     * returns price for given quantity scale
     */
    public function getOSPrice(int $quantityScale = 1): PriceInterface;

    /**
     * returns price info for given quantity scale.
     * price info might contain price and additional information for prices like discounts, ...
     */
    public function getOSPriceInfo(int $quantityScale = 1): PriceInfoInterface;

    /**
     * returns availability info based on given quantity
     */
    public function getOSAvailabilityInfo(?int $quantity = null): AvailabilityInterface;
}
