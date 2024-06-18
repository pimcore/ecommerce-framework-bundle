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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Exception;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\StatusInterface;

class PaymentNotSuccessfulException extends AbstractEcommerceException
{
    protected AbstractOrder $order;

    protected StatusInterface $status;

    /**
     * PaymentNotSuccessfulException constructor.
     *
     */
    public function __construct(AbstractOrder $order, StatusInterface $status, string $message)
    {
        parent::__construct($message);
        $this->order = $order;
        $this->status = $status;
    }

    public function getOrder(): AbstractOrder
    {
        return $this->order;
    }

    public function getStatus(): StatusInterface
    {
        return $this->status;
    }
}
