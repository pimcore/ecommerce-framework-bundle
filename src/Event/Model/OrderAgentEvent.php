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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model;

use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use Pimcore\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class OrderAgentEvent extends Event
{
    use ArgumentsAwareTrait;

    protected OrderAgentInterface $orderAgent;

    /**
     * OrderAgentEvent constructor.
     *
     */
    public function __construct(OrderAgentInterface $orderAgent, array $arguments = [])
    {
        $this->orderAgent = $orderAgent;
        $this->arguments = $arguments;
    }

    public function getOrderAgent(): OrderAgentInterface
    {
        return $this->orderAgent;
    }

    public function setOrderAgent(OrderAgentInterface $orderAgent): void
    {
        $this->orderAgent = $orderAgent;
    }
}
