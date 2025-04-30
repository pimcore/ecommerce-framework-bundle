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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement;

use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use Pimcore\Model\DataObject\Fieldcollection\Data\TaxEntry as TaxEntryFieldcollection;
use Pimcore\Model\DataObject\OnlineShopTaxClass;

class TaxEntry
{
    const CALCULATION_MODE_COMBINE = 'combine';

    const CALCULATION_MODE_ONE_AFTER_ANOTHER = 'oneAfterAnother';

    const CALCULATION_MODE_FIXED = 'fixed';

    protected ?TaxEntryFieldcollection $entry = null;

    protected float $percent;

    protected Decimal $amount;

    protected ?string $taxId = null;

    public function __construct(float $percent, Decimal $amount, ?string $taxId = null, ?TaxEntryFieldcollection $entry = null)
    {
        $this->percent = $percent;
        $this->amount = $amount;
        $this->taxId = $taxId;
        $this->entry = $entry;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): void
    {
        $this->percent = $percent;
    }

    public function setEntry(TaxEntryFieldcollection $entry): void
    {
        $this->entry = $entry;
    }

    public function getEntry(): TaxEntryFieldcollection
    {
        return $this->entry;
    }

    public function getAmount(): Decimal
    {
        return $this->amount;
    }

    public function setAmount(Decimal $amount): void
    {
        $this->amount = $amount;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId = null): void
    {
        $this->taxId = $taxId;
    }

    /**
     * Converts tax rate configuration of given OnlineShopTaxClass to TaxEntries that can be used for
     * tax calculation.
     *
     *
     * @return TaxEntry[]
     */
    public static function convertTaxEntries(OnlineShopTaxClass $taxClass): array
    {
        $convertedTaxEntries = [];
        if ($taxEntries = $taxClass->getTaxEntries()) {
            /** @var TaxEntryFieldcollection $entry */
            foreach ($taxEntries as $entry) {
                $convertedTaxEntries[] = new static(
                    $entry->getPercent(),
                    Decimal::create(0),
                    $entry->getName() . '-' . $entry->getPercent(),
                    $entry
                );
            }
        }

        return $convertedTaxEntries;
    }
}
