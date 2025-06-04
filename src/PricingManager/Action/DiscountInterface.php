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

use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;

interface DiscountInterface extends ActionInterface
{
    public function setAmount(float $amount): void;

    public function setPercent(float $percent): void;

    public function getAmount(): float;

    public function getPercent(): float;
}
