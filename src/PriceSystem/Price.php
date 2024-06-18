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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\Currency;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxCalculationService;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class Price implements PriceInterface
{
    private Currency $currency;

    private Decimal $grossAmount;

    private Decimal $netAmount;

    private ?string $taxEntryCombinationMode = TaxEntry::CALCULATION_MODE_COMBINE;

    private bool $minPrice;

    /**
     * @var TaxEntry[]
     */
    private array $taxEntries = [];

    public function __construct(Decimal $amount, Currency $currency, bool $minPrice = false)
    {
        $this->grossAmount = $this->netAmount = $amount;
        $this->currency = $currency;
        $this->minPrice = $minPrice;
    }

    public function __toString(): string
    {
        $string = $this->getCurrency()->toCurrency($this->grossAmount);

        return $string ?: '';
    }

    public function isMinPrice(): bool
    {
        return $this->minPrice;
    }

    public function setAmount(Decimal $amount, string $priceMode = self::PRICE_MODE_GROSS, bool $recalc = false): void
    {
        switch ($priceMode) {
            case self::PRICE_MODE_GROSS:
                $this->setGrossAmount($amount, $recalc);

                break;
            case self::PRICE_MODE_NET:
                $this->setNetAmount($amount, $recalc);

                break;
            default:
                throw new \InvalidArgumentException(sprintf('Price mode "%s" is not supported', $priceMode));
        }
    }

    public function getAmount(): Decimal
    {
        return $this->grossAmount;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getGrossAmount(): Decimal
    {
        return $this->grossAmount;
    }

    public function getNetAmount(): Decimal
    {
        return $this->netAmount;
    }

    /**
     * @return TaxEntry[]
     */
    public function getTaxEntries(): array
    {
        return $this->taxEntries;
    }

    public function getTaxEntryCombinationMode(): ?string
    {
        return $this->taxEntryCombinationMode;
    }

    public function setGrossAmount(Decimal $grossAmount, bool $recalc = false): void
    {
        $this->grossAmount = $grossAmount;

        if ($recalc) {
            $this->updateTaxes(TaxCalculationService::CALCULATION_FROM_GROSS);
        }
    }

    public function setNetAmount(Decimal $netAmount, bool $recalc = false): void
    {
        $this->netAmount = $netAmount;

        if ($recalc) {
            $this->updateTaxes(TaxCalculationService::CALCULATION_FROM_NET);
        }
    }

    public function setTaxEntries(array $taxEntries): void
    {
        $this->taxEntries = $taxEntries;
    }

    public function setTaxEntryCombinationMode(?string $taxEntryCombinationMode = null): void
    {
        $this->taxEntryCombinationMode = $taxEntryCombinationMode;
    }

    /**
     * Calls calculation service and updates taxes
     *
     */
    protected function updateTaxes(string $calculationMode): void
    {
        $taxCalculationService = new TaxCalculationService();
        $taxCalculationService->updateTaxes($this, $calculationMode);
    }
}
