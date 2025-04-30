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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Exception;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;

class PaymentNotAllowedException extends AbstractEcommerceException
{
    protected AbstractOrder $order;

    protected ?CartInterface $cart = null;

    protected ?bool $orderNeedsUpdate = null;

    /**
     * PaymentNotAllowedException constructor.
     *
     */
    public function __construct(string $message, AbstractOrder $order, ?CartInterface $cart = null, ?bool $orderNeedsUpdate = null)
    {
        parent::__construct($message);

        $this->order = $order;
        $this->cart = $cart;
        $this->orderNeedsUpdate = $orderNeedsUpdate;
    }
}
