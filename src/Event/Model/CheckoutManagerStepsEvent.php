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

use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutStepInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\CheckoutManagerInterface;
use Pimcore\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class CheckoutManagerStepsEvent extends Event
{
    use ArgumentsAwareTrait;

    protected ?CheckoutStepInterface $currentStep = null;

    protected CheckoutManagerInterface $checkoutManager;

    public function __construct(CheckoutManagerInterface $checkoutManager, ?CheckoutStepInterface $currentStep, array $arguments = [])
    {
        $this->checkoutManager = $checkoutManager;
        $this->currentStep = $currentStep;
        $this->arguments = $arguments;
    }

    public function getCurrentStep(): ?CheckoutStepInterface
    {
        return $this->currentStep;
    }

    public function setCurrentStep(?CheckoutStepInterface $currentStep): void
    {
        $this->currentStep = $currentStep;
    }

    public function getCheckoutManager(): CheckoutManagerInterface
    {
        return $this->checkoutManager;
    }

    public function setCheckoutManager(CheckoutManagerInterface $checkoutManager): void
    {
        $this->checkoutManager = $checkoutManager;
    }
}
