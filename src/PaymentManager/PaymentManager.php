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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\Exception\ProviderNotFoundException;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\PaymentInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;

class PaymentManager implements PaymentManagerInterface
{
    private PsrContainerInterface $providers;

    protected array $providerTypes;

    public function __construct(PsrContainerInterface $providers, array $providerTypes)
    {
        $this->providers = $providers;
        $this->providerTypes = $providerTypes;
    }

    public function getProviderTypes(): array
    {
        return $this->providerTypes;
    }

    public function getProvider(string $name): PaymentInterface
    {
        if (!$this->providers->has($name)) {
            throw new ProviderNotFoundException(sprintf(
                'The payment provider "%s" is not registered',
                $name
            ));
        }

        return $this->providers->get($name);
    }
}
