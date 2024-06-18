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
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;

/**
 * Interface for price implementations of online shop framework
 */
interface PriceInterface
{
    const PRICE_MODE_NET = 'net';

    const PRICE_MODE_GROSS = 'gross';

    /**
     * Returns $grossAmount
     *
     */
    public function getAmount(): Decimal;

    public function getCurrency(): Currency;

    public function isMinPrice(): bool;

    /**
     * Sets amount of price, depending on $priceMode and $recalc it sets net price or gross price and recalculates the
     * corresponding net or gross price.
     *
     * @param string $priceMode - default to PRICE_MODE_GROSS
     * @param bool $recalc - default to false
     */
    public function setAmount(Decimal $amount, string $priceMode = self::PRICE_MODE_GROSS, bool $recalc = false): void;

    /**
     * Returns gross amount of price
     *
     */
    public function getGrossAmount(): Decimal;

    /**
     * Returns net amount of price
     *
     */
    public function getNetAmount(): Decimal;

    /**
     * Returns tax entries of price as an array
     *
     * @return TaxEntry[]
     */
    public function getTaxEntries(): array;

    /**
     * Returns tax entry combination mode needed for tax calculation
     *
     */
    public function getTaxEntryCombinationMode(): ?string;

    /**
     * Sets gross amount of price. If $recalc is set to true, corresponding net price
     * is calculated based on tax entries and tax entry combination mode.
     *
     *
     */
    public function setGrossAmount(Decimal $grossAmount, bool $recalc = false): void;

    /**
     * Sets net amount of price. If $recalc is set to true, corresponding gross price
     * is calculated based on tax entries and tax entry combination mode.
     *
     *
     */
    public function setNetAmount(Decimal $netAmount, bool $recalc = false): void;

    /**
     * Sets tax entries for price.
     *
     *
     */
    public function setTaxEntries(array $taxEntries): void;

    /**
     * Sets $taxEntryCombinationMode for price.
     *
     *
     */
    public function setTaxEntryCombinationMode(?string $taxEntryCombinationMode = null): void;
}
