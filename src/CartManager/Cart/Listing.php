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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\Cart;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

/**
 * @method \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\Cart[] load()
 * @method \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\Cart|false current()
 * @method int getTotalCount()
 * @method \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\Cart\Listing\Dao getDao()
 * @method void setCartClass(string $cartClass)
 */
class Listing extends \Pimcore\Model\Listing\AbstractListing
{
    public function __construct()
    {
        $this->getDao()->setCartClass(Factory::getInstance()->getCartManager()->getCartClassName());
    }

    /**
     * @param string $key The key to check
     *
     */
    public function isValidOrderKey(string $key): bool
    {
        return in_array($key, ['userId', 'name', 'creationDateTimestamp', 'modificationDateTimestamp']);
    }

    public function getCarts(): array
    {
        return $this->getData();
    }

    public function setCarts(array $carts): static
    {
        return $this->setData($carts);
    }
}
