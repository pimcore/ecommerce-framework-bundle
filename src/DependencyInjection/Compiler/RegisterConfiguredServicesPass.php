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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\PimcoreEcommerceFrameworkExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class RegisterConfiguredServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerIndexServiceWorkers($container);
        $this->registerTrackingManagerTrackers($container);
        $this->registerPaymentManagerConfiguration($container);
    }

    public function registerIndexServiceWorkers(ContainerBuilder $container): void
    {
        $workers = [];
        foreach ($container->findTaggedServiceIds('pimcore_ecommerce.index_service.worker') as $id => $tags) {
            $workers[] = new Reference($id);
        }

        $indexService = $container->findDefinition(PimcoreEcommerceFrameworkExtension::SERVICE_ID_INDEX_SERVICE);
        $indexService->setArgument('$tenantWorkers', $workers);
    }

    public function registerTrackingManagerTrackers(ContainerBuilder $container): void
    {
        $trackers = [];

        foreach ($container->findTaggedServiceIds('pimcore_ecommerce.tracking.tracker') as $id => $tags) {
            $trackers[] = new Reference($id);
        }

        $trackingManager = $container->findDefinition(PimcoreEcommerceFrameworkExtension::SERVICE_ID_TRACKING_MANAGER);
        $trackingManager->setArgument('$trackers', $trackers);
    }

    private function registerPaymentManagerConfiguration(ContainerBuilder $container): void
    {
        $providerTypes = [];

        foreach ($container->findTaggedServiceIds('pimcore_ecommerce.payment.provider') as $id => $tags) {
            $providerTypes[$tags[0]['key']] = $id;
        }

        $paymentManager = $container->findDefinition(PimcoreEcommerceFrameworkExtension::SERVICE_ID_PAYMENT_MANAGER);
        $paymentManager->setArgument('$providerTypes', $providerTypes);
    }
}
