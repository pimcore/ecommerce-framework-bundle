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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter;

use Pimcore\Model\Asset;

class AssetId implements InterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): ?int
    {
        if (!empty($value) && $value instanceof Asset) {
            return $value->getId();
        }

        return null;
    }
}
