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

/**
 * Interface ProductInterface
 */
interface ProductInterface
{
    public function getId(): ?int;

    /**
     * called by default CommitOrderProcessor to get the product name to store it in the order item
     * should be overwritten in mapped sub classes of product classes
     *
     */
    public function getOSName(): ?string;

    /**
     * called by default CommitOrderProcessor to get the product number to store it in the order item
     * should be overwritten in mapped sub classes of product classes
     *
     */
    public function getOSProductNumber(): ?string;
}
