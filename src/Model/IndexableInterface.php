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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Model;

/**
 * Interface IndexableInterface
 */
interface IndexableInterface
{
    public function getId(): ?int;

    /**
     * defines if product is included into the product index. If false, product doesn't appear in product index.
     *
     */
    public function getOSDoIndexProduct(): bool;

    /**
     * defines the name of the price system for this product.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes
     *
     */
    public function getPriceSystemName(): ?string;

    /**
     * returns if product is active.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes in case of multiple criteria for product active state
     *
     *
     */
    public function isActive(bool $inProductList = false): bool;

    /**
     * returns product type for product index (either object or variant).
     * by default it returns type of object, but it may be overwritten if necessary.
     *
     */
    public function getOSIndexType(): ?string;

    /**
     * returns parent id for product index.
     * by default it returns id of parent object, but it may be overwritten if necessary.
     *
     */
    public function getOSParentId(): int|string|null;

    /**
     * returns array of categories.
     * has to be overwritten either in pimcore object or mapped sub class.
     *
     * @return \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory[]|null
     */
    public function getCategories(): ?array;

    /**
     * returns the class id of the object
     *
     */
    public function getClassId(): ?string;
}
