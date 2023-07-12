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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SessionBagListener implements EventSubscriberInterface
{
    const ATTRIBUTE_BAG_CART = 'ecommerceframework_cart';

    const ATTRIBUTE_BAG_ENVIRONMENT = 'ecommerceframework_environment';

    const ATTRIBUTE_BAG_PRICING_ENVIRONMENT = 'ecommerceframework_pricing_environment';

    const ATTRIBUTE_BAG_PAYMENT_ENVIRONMENT = 'ecommerceframework_payment_environment';

    public static function getSubscribedEvents(): array
    {
        return [
            //run after Symfony\Component\HttpKernel\EventListener\SessionListener
            KernelEvents::REQUEST => ['onKernelRequest', 127],
        ];
    }

    /**
     * @return string[]
     */
    protected function getBagNames(): array
    {
        return [
            self::ATTRIBUTE_BAG_CART,
            self::ATTRIBUTE_BAG_ENVIRONMENT,
            self::ATTRIBUTE_BAG_PRICING_ENVIRONMENT,
            self::ATTRIBUTE_BAG_PAYMENT_ENVIRONMENT,
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->attributes->get('_stateless', false)) {
            return;
        }

        $session = $event->getRequest()->getSession();

        //do not register bags, if session is already started
        if ($session->isStarted()) {
            return;
        }

        $this->configure($session);
    }

    public function configure(SessionInterface $session): void
    {
        $bagNames = $this->getBagNames();

        foreach ($bagNames as $bagName) {
            $bag = new AttributeBag('_' . $bagName);
            $bag->setName($bagName);

            $session->registerBag($bag);
        }
    }

    /**
     * Clears all session bags filled from the e-commerce framework
     *
     * @param SessionInterface $session
     */
    public function clearSession(SessionInterface $session): void
    {
        $bagNames = $this->getBagNames();

        foreach ($bagNames as $bagName) {
            $sessionBag = $session->getBag($bagName);
            $sessionBag->clear();
        }
    }
}
