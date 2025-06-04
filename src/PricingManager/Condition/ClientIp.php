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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

class ClientIp implements ConditionInterface
{
    protected int $ip;

    public function check(EnvironmentInterface $environment): bool
    {
        $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['REMOTE_ADDR'];

        return $clientIp == $this->getIp();
    }

    public function toJSON(): string
    {
        // basic
        $json = [
            'type' => 'ClientIp', 'ip' => $this->getIp(),
        ];

        return json_encode($json);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $this->setIp($json->ip);

        return $this;
    }

    public function getIp(): int
    {
        return $this->ip;
    }

    public function setIp(int $ip): void
    {
        $this->ip = $ip;
    }
}
