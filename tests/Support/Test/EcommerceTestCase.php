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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tests\Support\Test;

use Pimcore\Bundle\EcommerceFrameworkBundle\Environment;
use Pimcore\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\EventListener\SessionBagListener;
use Pimcore\Localization\LocaleService;
use Pimcore\Tests\Support\Test\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

abstract class EcommerceTestCase extends TestCase
{
    private ?EnvironmentInterface $environment = null;

    private ?SessionInterface $session = null;

    protected function buildEnvironment(): EnvironmentInterface
    {
        if (null === $this->environment) {
            $this->environment = new Environment(new LocaleService());
        }

        return $this->environment;
    }

    protected function buildSession(): SessionInterface
    {
        if (null === $this->session) {
            $this->session = new Session(new MockArraySessionStorage());

            $configurator = new SessionBagListener();
            $configurator->configure($this->session);

            $this->session->getBag(SessionBagListener::ATTRIBUTE_BAG_CART)->set('carts', []);

            $requestStack = \Pimcore::getContainer()->get('request_stack');
            if (!$request = $requestStack->getCurrentRequest()) {
                $request = new Request();
                $requestStack->push($request);
            }

            $request->setSession($this->session);
        }

        return $this->session;
    }
}
