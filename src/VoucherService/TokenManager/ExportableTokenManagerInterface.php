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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager;

interface ExportableTokenManagerInterface
{
    const FORMAT_CSV = 'csv';

    const FORMAT_PLAIN = 'plain';

    /**
     * Export tokens to CSV
     *
     *
     */
    public function exportCsv(array $params): string;

    /**
     * Export tokens to plain text list
     *
     *
     */
    public function exportPlain(array $params): string;
}
