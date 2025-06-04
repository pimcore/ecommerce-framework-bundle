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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData;

/**
 * @method CartCheckoutData[] load()
 * @method CartCheckoutData|false current()
 * @method int getTotalCount()
 * @method \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Listing\Dao getDao()
 */
class Listing extends \Pimcore\Model\Listing\AbstractListing
{
    public function isValidOrderKey(string $key): bool
    {
        if ($key == 'key' || $key == 'cartId') {
            return true;
        }

        return false;
    }

    public function getCartCheckoutDataItems(): array
    {
        return $this->getData();
    }

    public function setCartCheckoutDataItems(array $cartCheckoutDataItems): Listing
    {
        return $this->setData($cartCheckoutDataItems);
    }
}
