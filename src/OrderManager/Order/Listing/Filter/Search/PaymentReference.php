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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter\Search;

use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter\AbstractSearch;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderListInterface;

class PaymentReference extends AbstractSearch
{
    protected function getConditionColumn(): string
    {
        return 'paymentInfo.paymentReference';
    }

    protected function getConditionValue(): string
    {
        $value = parent::getConditionValue();
        $value = ',' . $value . ',';

        return $value;
    }

    /**
     * Join paymentInfo
     *
     */
    protected function prepareApply(OrderListInterface $orderList): void
    {
        $orderList->joinPaymentInfo();
    }
}
