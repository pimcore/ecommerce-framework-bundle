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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentResponse;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;

class JsonResponse extends AbstractResponse
{
    protected string $jsonString;

    /**
     * JsonResponse constructor.
     *
     */
    public function __construct(AbstractOrder $order, string $jsonString)
    {
        parent::__construct($order);
        $this->jsonString = $jsonString;
    }

    public function getJsonString(): string
    {
        return $this->jsonString;
    }
}
