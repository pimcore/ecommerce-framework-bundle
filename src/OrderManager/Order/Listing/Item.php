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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder as Order;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrderItem as OrderItem;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\AbstractOrderListItem;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListItemInterface;
use Pimcore\Model\DataObject\Concrete;

class Item extends AbstractOrderListItem implements OrderListItemInterface
{
    public function getId(): int
    {
        return $this->resultRow['Id'];
    }

    /**
     * @throws \Exception
     */
    public function __call(string $method, array $args): mixed
    {
        $field = substr($method, 3);
        if (substr($method, 0, 3) == 'get' && array_key_exists($field, $this->resultRow)) {
            return $this->resultRow[$field];
        }

        $object = $this->reference();
        if ($object) {
            return call_user_func_array([$object, $method], $args);
        }

        throw new \Exception("Object with {$this->getId()} not found.");
    }

    public function reference(): OrderItem|Order|null
    {
        $object = Concrete::getById($this->getId());

        if ($object instanceof Order || $object instanceof OrderItem) {
            return $object;
        }

        return null;
    }
}
