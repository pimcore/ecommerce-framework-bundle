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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\ModificatedPrice;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class Discount implements DiscountInterface
{
    protected Decimal $amount;

    protected ?RuleInterface $rule = null;

    public function __construct(RuleInterface $rule)
    {
        $this->rule = $rule;
        $this->amount = Decimal::create(0);
    }

    /**
     * modificator name
     *
     */
    public function getName(): string
    {
        if ($this->rule) {
            return $this->rule->getName();
        }

        return 'discount';
    }

    /**
     * modify price
     *
     *
     */
    public function modify(PriceInterface $currentSubTotal, CartInterface $cart): ModificatedPrice
    {
        $amount = $this->getAmount();
        if ($currentSubTotal->getAmount()->lessThan($amount->toAdditiveInverse())) {
            $amount = $currentSubTotal->getAmount()->toAdditiveInverse();
        }

        $modificatedPrice = new ModificatedPrice($amount, $currentSubTotal->getCurrency(), false, $this->rule->getLabel());
        $modificatedPrice->setRule($this->rule);

        $taxClass = Factory::getInstance()->getPriceSystem('default')->getTaxClassForPriceModification($this);

        $modificatedPrice->setTaxEntryCombinationMode($taxClass->getTaxEntryCombinationType());
        $modificatedPrice->setTaxEntries(TaxEntry::convertTaxEntries($taxClass));
        $modificatedPrice->setGrossAmount($amount, true);

        return $modificatedPrice;
    }

    public function setAmount(Decimal $amount): DiscountInterface|static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount(): Decimal
    {
        return $this->amount;
    }

    public function getRuleId(): ?int
    {
        return $this->rule ? $this->rule->getId() : null;
    }
}
