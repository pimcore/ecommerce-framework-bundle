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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutStepInterface as CheckoutManagerCheckoutStepInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\EventListener\Frontend\TrackingCodeFlashMessageListener;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\ProductInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class TrackingManager implements TrackingManagerInterface
{
    /**
     * @var TrackerInterface[]
     */
    protected array $trackers = [];

    /**
     * @var TrackerInterface[]
     */
    protected array $activeTrackerCache = [];

    protected ?string $cachedAssortmentTenant = null;

    protected ?string $cachedCheckoutTenant = null;

    protected ?EnvironmentInterface $enviroment = null;

    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, EnvironmentInterface $environment, array $trackers = [])
    {
        foreach ($trackers as $tracker) {
            $this->registerTracker($tracker);
        }

        $this->requestStack = $requestStack;
        $this->enviroment = $environment;
    }

    /**
     * Register a tracker
     *
     */
    public function registerTracker(TrackerInterface $tracker): void
    {
        $this->trackers[] = $tracker;
    }

    /**
     * Get all registered trackers
     *
     * @return TrackerInterface[]
     */
    public function getTrackers(): array
    {
        return $this->trackers;
    }

    /**
     * Get all for current tenants active trackers
     *
     * @return TrackerInterface[]
     */
    public function getActiveTrackers(): array
    {
        $currentAssortmentTenant = $this->enviroment->getCurrentAssortmentTenant() ?: 'default';
        $currentCheckoutTenant = $this->enviroment->getCurrentCheckoutTenant() ?: 'default';

        if ($currentAssortmentTenant !== $this->cachedAssortmentTenant || $currentCheckoutTenant !== $this->cachedCheckoutTenant) {
            $this->cachedCheckoutTenant = $currentCheckoutTenant;
            $this->cachedAssortmentTenant = $currentAssortmentTenant;

            $this->activeTrackerCache = [];
            foreach ($this->trackers as $tracker) {
                $active = false;
                if (empty($tracker->getAssortmentTenants()) || in_array($currentAssortmentTenant, $tracker->getAssortmentTenants())) {
                    $active = true;
                }
                if (empty($tracker->getCheckoutTenants()) || in_array($currentCheckoutTenant, $tracker->getCheckoutTenants())) {
                    $active = true;
                }

                if ($active) {
                    $this->activeTrackerCache[] = $tracker;
                }
            }
        }

        return $this->activeTrackerCache;
    }

    /**
     * Tracks a category page view
     *
     * @param array|string $category One or more categories matching the page
     * @param mixed $page            Any kind of page information you can use to track your page
     */
    public function trackCategoryPageView(array|string $category, mixed $page = null): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CategoryPageViewInterface) {
                $tracker->trackCategoryPageView($category, $page);
            }
        }
    }

    /**
     * Track product impression
     *
     */
    public function trackProductImpression(ProductInterface $product, string $list = 'default'): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof ProductImpressionInterface) {
                $tracker->trackProductImpression($product, $list);
            }
        }
    }

    /**
     * Track product view
     *
     */
    public function trackProductView(ProductInterface $product): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof ProductViewInterface) {
                $tracker->trackProductView($product);
            }
        }
    }

    /**
     * Track a cart update
     *
     */
    public function trackCartUpdate(CartInterface $cart): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CartUpdateInterface) {
                $tracker->trackCartUpdate($cart);
            }
        }
    }

    /**
     * Track product add to cart
     *
     */
    public function trackCartProductActionAdd(CartInterface $cart, ProductInterface $product, float|int $quantity = 1): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CartProductActionAddInterface) {
                $tracker->trackCartProductActionAdd($cart, $product, $quantity);
            }
        }
    }

    /**
     * Track product remove from cart
     *
     */
    public function trackCartProductActionRemove(CartInterface $cart, ProductInterface $product, float|int $quantity = 1): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CartProductActionRemoveInterface) {
                $tracker->trackCartProductActionRemove($cart, $product, $quantity);
            }
        }
    }

    /**
     * Track start checkout with first step
     *
     */
    public function trackCheckout(CartInterface $cart): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CheckoutInterface) {
                $tracker->trackCheckout($cart);
            }
        }
    }

    /**
     * Track checkout complete
     *
     */
    public function trackCheckoutComplete(AbstractOrder $order): void
    {
        if ($order->getProperty('os_tracked')) {
            return;
        }

        // add property to order object in order to prevent multiple checkout complete tracking
        $order->setProperty('os_tracked', 'bool', true);
        $order->save();

        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CheckoutCompleteInterface) {
                $tracker->trackCheckoutComplete($order);
            }
        }
    }

    /**
     * Track checkout step
     *
     */
    public function trackCheckoutStep(CheckoutManagerCheckoutStepInterface $step, CartInterface $cart, string $stepNumber = null, string $checkoutOption = null): void
    {
        foreach ($this->getActiveTrackers() as $tracker) {
            if ($tracker instanceof CheckoutStepInterface) {
                $tracker->trackCheckoutStep($step, $cart, $stepNumber, $checkoutOption);
            }
        }
    }

    public function getTrackedCodes(): string
    {
        $result = '';
        foreach ($this->getTrackers() as $tracker) {
            if ($tracker instanceof TrackingCodeAwareInterface) {
                if (count($tracker->getTrackedCodes())) {
                    $result .= implode(PHP_EOL, $tracker->getTrackedCodes()).PHP_EOL.PHP_EOL;
                }
            }
        }

        return $result;
    }

    public function forwardTrackedCodesAsFlashMessage(): static
    {
        $trackedCodes = [];

        foreach ($this->getTrackers() as $tracker) {
            if ($tracker instanceof TrackingCodeAwareInterface) {
                if (count($tracker->getTrackedCodes())) {
                    $trackedCodes[get_class($tracker)] = $tracker->getTrackedCodes();
                }
            }
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->set(TrackingCodeFlashMessageListener::FLASH_MESSAGE_BAG_KEY, $trackedCodes);

        return $this;
    }

    public function trackEvent(
        string $eventCategory,
        string $eventAction,
        string $eventLabel = null,
        int $eventValue = null
    ): void {
        foreach ($this->getTrackers() as $tracker) {
            if ($tracker instanceof TrackEventInterface) {
                $tracker->trackEvent($eventCategory, $eventAction, $eventLabel, $eventValue);
            }
        }
    }
}
