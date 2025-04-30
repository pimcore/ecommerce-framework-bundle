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
            $db->quote((string)$this->product->getId()),
        ];

        $variants = $this->product->getChildren([
            \Pimcore\Model\DataObject\Concrete::OBJECT_TYPE_VARIANT,
        ]);

        /** @var \Pimcore\Model\DataObject\Concrete $variant */
        foreach ($variants as $variant) {
            $ids[] = $db->quote((string)$variant->getId());
        }

        $orderList->addCondition('orderItem.product__id IN (' . implode(',', $ids) . ')');

        return $this;
    }
}
