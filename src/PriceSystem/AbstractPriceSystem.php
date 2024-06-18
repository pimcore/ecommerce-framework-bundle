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

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\CartPriceModificatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxCalculationService;
use Pimcore\Bundle\EcommerceFrameworkBundle\PriceSystem\TaxManagement\TaxEntry;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManagerLocatorInterface;
use Pimcore\Model\DataObject\OnlineShopTaxClass;
use Pimcore\Model\WebsiteSetting;

abstract class AbstractPriceSystem implements PriceSystemInterface
{
    protected PricingManagerLocatorInterface $pricingManagers;

    public function __construct(PricingManagerLocatorInterface $pricingManagers)
    {
        $this->pricingManagers = $pricingManagers;
    }

    public function getPriceInfo(CheckoutableInterface $product, int|string $quantityScale = null, array $products = null): PriceInfoInterface
    {
        return $this->initPriceInfoInstance($quantityScale, $product, $products);
    }

    /**
     * Returns shop-instance specific implementation of priceInfo, override this method in your own price system to
     * set any price values
     *
     * @param int|string|null $quantityScale Numeric or string (allowed values: PriceInfoInterface::MIN_PRICE)
     * @param CheckoutableInterface[] $products
     *
     */
    protected function initPriceInfoInstance(int|string|null $quantityScale, CheckoutableInterface $product, array $products): PriceInfoInterface
    {
        $priceInfo = $this->createPriceInfoInstance($quantityScale, $product, $products);

        if ($quantityScale !== PriceInfoInterface::MIN_PRICE) {
            $priceInfo->setQuantity($quantityScale);
        }

        $priceInfo->setProduct($product);
        $priceInfo->setProducts($products);
        $priceInfo->setPriceSystem($this);

        // apply pricing rules
        $priceInfoWithRules = $this->getPricingManager()->applyProductRules($priceInfo);

        return $priceInfoWithRules;
    }

    protected function getPricingManager(): PricingManagerInterface
    {
        return $this->pricingManagers->getPricingManager();
    }

    /**
     * @param int|string|null $quantityScale Numeric or string (allowed values: PriceInfoInterface::MIN_PRICE)
     * @param CheckoutableInterface[] $products
     *
     */
    abstract public function createPriceInfoInstance(int|string|null $quantityScale, CheckoutableInterface $product, array $products): AbstractPriceInfo;

    /**
     * Sample implementation for getting the correct OnlineShopTaxClass. In this case Tax Class is retrieved from
     * Website Setting and if no Website Setting is set it creates an empty new Tax Class.
     *
     * Should be overwritten in custom price systems with suitable implementation.
     *
     */
    protected function getDefaultTaxClass(): OnlineShopTaxClass
    {
        $taxClass = WebsiteSetting::getByName('defaultTaxClass');

        if ($taxClass) {
            $taxClass = $taxClass->getData();
        }

        if (empty($taxClass)) {
            $taxClass = new OnlineShopTaxClass();
            $taxClass->setTaxEntryCombinationType(TaxEntry::CALCULATION_MODE_COMBINE);
        }

        return $taxClass;
    }

    /**
     * Returns OnlineShopTaxClass for given CheckoutableInterface.
     *
     *
     */
    public function getTaxClassForProduct(CheckoutableInterface $product): OnlineShopTaxClass
    {
        return $this->getDefaultTaxClass();
    }

    /**
     * Returns OnlineShopTaxClass for given CartPriceModificatorInterface
     *
     *
     */
    public function getTaxClassForPriceModification(CartPriceModificatorInterface $modificator): OnlineShopTaxClass
    {
        return $this->getDefaultTaxClass();
    }

    protected function getTaxCalculationService(): TaxCalculationService
    {
        return new TaxCalculationService();
    }
}
