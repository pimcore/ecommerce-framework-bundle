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

interface CachingPriceSystemInterface extends PriceSystemInterface
{
    /**
     * Loads price infos once for given product entries and caches them
     *
     *
     */
    public function loadPriceInfos(array $productEntries, array $options): mixed;

    /**
     * Clears cached price infos
     *
     *
     */
    public function clearPriceInfos(array $productEntries, array $options): mixed;
}
