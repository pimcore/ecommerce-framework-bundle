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

use Pimcore\Model\DataObject\AbstractObject;

class DefaultObjects implements RelationInterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): array
    {
        $result = [];

        if (is_array($value)) {
            foreach ($value as $v) {
                $result[] = ['dest' => $v->getId(), 'type' => 'object'];
            }
        } elseif ($value instanceof AbstractObject) {
            $result[] = ['dest' => $value->getId(), 'type' => 'object'];
        }

        return $result;
    }
}
