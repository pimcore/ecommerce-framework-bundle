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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Action;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;

/**
 * Adds a gift product to the given cart
 */
interface GiftInterface extends ActionInterface, CartActionInterface
{
    /**
     * Set gift product
     *
     *
     */
    public function setProduct(AbstractProduct $product): GiftInterface;

    public function getProduct(): ?AbstractProduct;
}
