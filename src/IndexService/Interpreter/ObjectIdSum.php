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

use Pimcore\Model\Element\ElementInterface;

class ObjectIdSum implements InterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): ?int
    {
        $sum = 0;
        if (is_array($value)) {
            foreach ($value as $object) {
                if ($object instanceof ElementInterface) {
                    $sum += $object->getId();
                }
            }
        }

        return $sum;
    }
}
