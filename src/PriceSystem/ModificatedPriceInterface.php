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

use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;

/**
 * Interface for prices returned by price modifcators
 */
interface ModificatedPriceInterface extends PriceInterface
{
    public function getDescription(): string;

    public function getRule(): ?RuleInterface;
}
