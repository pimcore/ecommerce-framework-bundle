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

use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;

/**
 * Price system which caches created price info objects per product and request
 */
abstract class CachingPriceSystem extends AbstractPriceSystem implements CachingPriceSystemInterface
{
    /**
     * @var PriceInfoInterface[][] $priceInfos
     */
    protected array $priceInfos = [];

    public function getPriceInfo(CheckoutableInterface $product, int|string|null $quantityScale = null, ?array $products = null): PriceInfoInterface
    {
        $pId = $product->getId();
        if (!is_array($this->priceInfos[$pId] ?? null)) {
            $this->priceInfos[$pId] = [];
        }

        $quantityScaleKey = (string) $quantityScale;

        if (empty($this->priceInfos[$pId][$quantityScaleKey])) {
            $priceInfo = $this->initPriceInfoInstance($quantityScale, $product, $products ?? []);
            $this->priceInfos[$pId][$quantityScaleKey] = $priceInfo;
        }

        return $this->priceInfos[$pId][$quantityScaleKey];
    }

    public function loadPriceInfos(array $productEntries, array $options): mixed
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . get_class($this));
    }

    public function clearPriceInfos(array $productEntries, array $options): mixed
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . get_class($this));
    }

    public function filterProductIds(array $productIds, ?float $fromPrice, ?float $toPrice, string $order, int $offset, int $limit): array
    {
        throw new UnsupportedException(__METHOD__  . ' is not supported for ' . get_class($this));
    }
}
