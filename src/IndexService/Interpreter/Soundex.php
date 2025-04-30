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

class Soundex implements InterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): int
    {
        if (is_array($value)) {
            sort($value);
            $string = implode(' ', $value);
        } else {
            $string = (string)$value;
        }
        $soundex = soundex($string);

        return (int)(ord(substr($soundex, 0, 1)).substr($soundex, 1));
    }
}
