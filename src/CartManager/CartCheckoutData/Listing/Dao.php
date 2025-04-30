<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Listing;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData;

/**
 * @internal
 *
 * @property CartCheckoutData\Listing $model
 */
class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao
{
    public function load(): array
    {
        $items = [];

        $cartCheckoutDataItems = $this->db->fetchAllAssociative('SELECT cartid, `key` FROM ' . \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Dao::TABLE_NAME .
                                                 $this->getCondition() . $this->getOrder() . $this->getOffsetLimit());

        foreach ($cartCheckoutDataItems as $item) {
            $items[] = \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData::getByKeyCartId($item['key'], $item['cartid']);
        }
        $this->model->setCartCheckoutDataItems($items);

        return $items;
    }

    public function getTotalCount(): int
    {
        try {
            return (int)$this->db->fetchOne('SELECT COUNT(*) FROM `' . \Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartCheckoutData\Dao::TABLE_NAME . '`' . $this->getCondition());
        } catch (\Exception $e) {
            return 0;
        }
    }
}
