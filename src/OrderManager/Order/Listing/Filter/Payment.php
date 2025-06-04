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

class Payment implements OrderListFilterInterface
{
    const PAYMENT_STATE_OK = 'ok';

    const PAYMENT_STATE_FAIL = 'fail';

    protected string $value;

    /**
     * Allowed origin values
     *
     */
    protected array $allowedValues = [
        self::PAYMENT_STATE_OK,
        self::PAYMENT_STATE_FAIL,
    ];

    public function __construct(string $paymentState)
    {
        if (!in_array($paymentState, $this->allowedValues)) {
            throw new \InvalidArgumentException('Invalid filter value');
        }

        $this->value = $paymentState;
    }

    public function apply(OrderListInterface $orderList): static
    {
        switch ($this->value) {
            case self::PAYMENT_STATE_OK:
                $orderList->addCondition('order.paymentAuthorizedData_aliasCC IS NOT NULL');

                break;

            case self::PAYMENT_STATE_FAIL:
                $orderList->addCondition('order.paymentAuthorizedData_aliasCC IS NULL');

                break;
        }

        return $this;
    }
}
