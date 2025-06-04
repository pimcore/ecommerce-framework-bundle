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
