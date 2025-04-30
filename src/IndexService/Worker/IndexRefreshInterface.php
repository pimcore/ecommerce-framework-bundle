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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker;

interface IndexRefreshInterface
{
    public function startReindexMode(): void;

    public function updateSynonyms(
        string $indexNameOverride = '',
        bool $skipComparison = false,
        bool $skipLocking = true
    ): void;
}
