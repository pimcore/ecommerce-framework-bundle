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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface as PriceSystemPriceInfoInterface;

interface PricingManagerInterface
{
    public function applyProductRules(PriceSystemPriceInfoInterface $priceinfo): PriceSystemPriceInfoInterface;

    /**
     *
     * @return RuleInterface[] applied rules
     */
    public function applyCartRules(CartInterface $cart): array;

    /**
     * Get map from action name to used class
     *
     */
    public function getActionMapping(): array;

    /**
     * Get map from condition name to used class
     *
     */
    public function getConditionMapping(): array;

    /**
     * Factory
     *
     *
     *
     * @throws InvalidConfigException
     */
    public function getCondition(string $type): ConditionInterface;

    /**
     * Factory
     *
     *
     */
    public function getAction(string $type): ActionInterface;

    /**
     * Factory
     *
     */
    public function getEnvironment(): EnvironmentInterface;

    /**
     * Wraps price info in pricing manager price info
     *
     *
     */
    public function getPriceInfo(PriceSystemPriceInfoInterface $priceInfo): PriceInfoInterface|PriceSystemPriceInfoInterface;
}
