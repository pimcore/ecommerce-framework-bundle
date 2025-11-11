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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter;

use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListFilterInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;

class Product implements OrderListFilterInterface
{
    protected \Pimcore\Model\DataObject\Concrete $product;

    public function __construct(\Pimcore\Model\DataObject\Concrete $product)
    {
        $this->product = $product;
    }

    public function apply(OrderListInterface $orderList): static
    {
        $db = \Pimcore\Db::get();
        $ids = [
            $this->product->getId() ?? 0,
        ];

        $variants = $this->product->getChildren([
            \Pimcore\Model\DataObject\Concrete::OBJECT_TYPE_VARIANT,
        ]);

        /** @var \Pimcore\Model\DataObject\Concrete $variant */
        foreach ($variants as $variant) {
            $ids[] = $variant->getId() ?? 0;
        }

        $orderList->addCondition('orderItem.product__id IN (' . implode(',', $ids) . ')');

        return $this;
    }
}
