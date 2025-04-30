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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Action;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\ShippingInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class FreeShipping implements ActionInterface, CartActionInterface
{
    public function executeOnCart(EnvironmentInterface $environment): ActionInterface
    {
        $priceCalculator = $environment->getCart()->getPriceCalculator();

        $list = $priceCalculator->getModificators();
        foreach ($list as &$modificator) {
            // remove shipping charge
            if ($modificator instanceof ShippingInterface) {
                $modificator->setCharge(Decimal::zero());
                $priceCalculator->calculate(true);
            }
        }

        return $this;
    }

    public function toJSON(): string
    {
        return json_encode([
            'type' => 'FreeShipping',
        ]);
    }

    public function fromJSON(string $string): ActionInterface
    {
        return $this;
    }
}
