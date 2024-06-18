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

namespace Pimcore\Bundle\EcommerceFrameworkBundle;

use Pimcore\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\AvailabilitySystem\AvailabilitySystemLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartManagerLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutManagerFactoryLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\CheckoutManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\PimcoreEcommerceFrameworkExtension;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterService;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterServiceLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\IndexService;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherTokenType;
use Pimcore\Bundle\EcommerceFrameworkBundle\OfferTool\ServiceInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\OrderManagerLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\V7\OrderManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\PaymentManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceSystemLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\TrackingManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager\TokenManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\VoucherServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Factory
{
    private ContainerInterface $container;

    /**
     * Tenant specific cart managers
     *
     */
    private CartManagerLocatorInterface $cartManagers;

    /**
     * Tenant specific order managers
     *
     */
    private OrderManagerLocatorInterface $orderManagers;

    /**
     * Pricing managers registered by tenant
     *
     */
    private PricingManagerLocatorInterface $pricingManagers;

    /**
     * Price systems registered by name
     *
     */
    private PriceSystemLocatorInterface $priceSystems;

    /**
     * Availability systems registered by name
     *
     */
    private AvailabilitySystemLocatorInterface $availabilitySystems;

    /**
     * Checkout manager factories registered by tenant
     *
     */
    private CheckoutManagerFactoryLocatorInterface $checkoutManagerFactories;

    /**
     * Commit order processors registered by tenant
     *
     */
    private CommitOrderProcessorLocatorInterface $commitOrderProcessors;

    /**
     * Filter services registered by ^tenant
     *
     */
    private FilterServiceLocatorInterface $filterServices;

    /**
     * Systems with multiple instances (e.g. price systems or tenant specific systems) are
     * injected through a service locator which is indexed by tenant/name. All other services
     * are loaded from the container on demand to make sure only services needed are built.
     *
     */
    public function __construct(
        ContainerInterface $container,
        CartManagerLocatorInterface $cartManagers,
        OrderManagerLocatorInterface $orderManagers,
        PricingManagerLocatorInterface $pricingManagers,
        PriceSystemLocatorInterface $priceSystems,
        AvailabilitySystemLocatorInterface $availabilitySystems,
        CheckoutManagerFactoryLocatorInterface $checkoutManagerFactories,
        CommitOrderProcessorLocatorInterface $commitOrderProcessors,
        FilterServiceLocatorInterface $filterServices
    ) {
        $this->container = $container;
        $this->cartManagers = $cartManagers;
        $this->orderManagers = $orderManagers;
        $this->pricingManagers = $pricingManagers;
        $this->priceSystems = $priceSystems;
        $this->availabilitySystems = $availabilitySystems;
        $this->checkoutManagerFactories = $checkoutManagerFactories;
        $this->commitOrderProcessors = $commitOrderProcessors;
        $this->filterServices = $filterServices;
    }

    public static function getInstance(): self
    {
        return \Pimcore::getContainer()->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_FACTORY);
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_ENVIRONMENT);
    }

    /**
     * Returns cart manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     *
     *
     */
    public function getCartManager(string $tenant = null): CartManagerInterface
    {
        return $this->cartManagers->getCartManager($tenant);
    }

    /**
     * Returns order manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     *
     *
     */
    public function getOrderManager(string $tenant = null): OrderManagerInterface
    {
        return $this->orderManagers->getOrderManager($tenant);
    }

    /**
     * Returns pricing manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     *
     *
     */
    public function getPricingManager(string $tenant = null): PricingManagerInterface
    {
        return $this->pricingManagers->getPricingManager($tenant);
    }

    /**
     * Returns a price system by name. Falls back to "default" if no name is passed.
     *
     *
     */
    public function getPriceSystem(string $name = null): PriceSystemInterface
    {
        return $this->priceSystems->getPriceSystem($name);
    }

    /**
     * Returns an availability system by name. Falls back to "default" if no name is passed.
     *
     *
     */
    public function getAvailabilitySystem(string $name = null): AvailabilitySystemInterface
    {
        return $this->availabilitySystems->getAvailabilitySystem($name);
    }

    /**
     * Returns checkout manager for a specific tenant. If no tenant is passed it will fall back to the current
     * checkout tenant or to "default" if no current checkout tenant is set.
     *
     *
     */
    public function getCheckoutManager(CartInterface $cart, string $tenant = null): CheckoutManagerInterface
    {
        $factory = $this->checkoutManagerFactories->getCheckoutManagerFactory($tenant);

        return $factory->createCheckoutManager($cart);
    }

    /**
     * Returns a commit order processor which is configured for a specific checkout manager
     *
     *
     */
    public function getCommitOrderProcessor(string $tenant = null): CommitOrderProcessorInterface
    {
        return $this->commitOrderProcessors->getCommitOrderProcessor($tenant);
    }

    public function getPaymentManager(): PaymentManagerInterface
    {
        return $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_PAYMENT_MANAGER);
    }

    /**
     * Returns the index service which holds a collection of all index workers
     *
     */
    public function getIndexService(): IndexService
    {
        return $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_INDEX_SERVICE);
    }

    /**
     * Returns the filter service for the currently set assortment tenant. Falls back to "default" if no tenant is passed
     * and there is no current assortment tenant set.
     *
     *
     */
    public function getFilterService(string $tenant = null): FilterService
    {
        return $this->filterServices->getFilterService($tenant);
    }

    public function getAllTenants(): array
    {
        return $this->getIndexService()->getTenants();
    }

    public function getOfferToolService(): ServiceInterface
    {
        return $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_OFFER_TOOL);
    }

    public function getVoucherService(): VoucherServiceInterface
    {
        return $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_VOUCHER_SERVICE);
    }

    /**
     * Builds a token manager for a specific token configuration
     *
     *
     */
    public function getTokenManager(AbstractVoucherTokenType $configuration): TokenManagerInterface
    {
        $tokenManagerFactory = $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_TOKEN_MANAGER_FACTORY);

        return $tokenManagerFactory->getTokenManager($configuration);
    }

    public function getTrackingManager(): TrackingManagerInterface
    {
        return $this->container->get(PimcoreEcommerceFrameworkExtension::SERVICE_ID_TRACKING_MANAGER);
    }

    public function saveState(): void
    {
        $this->getCartManager()->save();
        $this->getEnvironment()->save();
    }
}
