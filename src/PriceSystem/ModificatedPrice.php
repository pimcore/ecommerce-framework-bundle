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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\Currency;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Type\Decimal;

class ModificatedPrice extends Price implements ModificatedPriceInterface
{
    protected ?string $description = null;

    protected ?RuleInterface $rule = null;

    public function __construct(Decimal $amount, Currency $currency, bool $minPrice = false, ?string $description = null)
    {
        parent::__construct($amount, $currency, $minPrice);

        $this->description = $description;
    }

    public function getRule(): ?RuleInterface
    {
        return $this->rule;
    }

    /**
     * @return $this
     */
    public function setRule(?RuleInterface $rule): static
    {
        $this->rule = $rule;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * @return $this
     */
    public function setDescription(?string $description = null): static
    {
        $this->description = $description;

        return $this;
    }
}
