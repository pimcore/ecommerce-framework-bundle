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

/**
 * Base implementation for a lazy loading price info
 */
class LazyLoadingPriceInfo extends AbstractPriceInfo implements PriceInfoInterface
{
    /**
     * @var PriceInfoInterface[]
     */
    protected array $priceRegistry = [];

    public static function getInstance(): static
    {
        return parent::getInstance();
    }

    public function __call(string $name, array $arg): mixed
    {
        if (array_key_exists($name, $this->priceRegistry)) {
            return $this->priceRegistry[$name];
        } else {
            if (method_exists($this, '_' . $name)) {
                $priceInfo = $this->{'_' . $name}();
            } elseif (method_exists($this->getPriceSystem(), $name)) {
                $method = $name;
                $priceInfo = $this->getPriceSystem()->$method($this->getProduct(), $this->getQuantity(), $this->getProducts());
            } else {
                throw new \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException($name . ' is not supported for ' . get_class($this));
            }
            if ($priceInfo != null && method_exists($priceInfo, 'setPriceSystem')) {
                $priceInfo->setPriceSystem($this->getPriceSystem());
            }
            $this->priceRegistry[$name] = $priceInfo;
        }

        return $this->priceRegistry[$name];
    }
}
