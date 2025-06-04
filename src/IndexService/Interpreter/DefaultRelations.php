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

use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

class DefaultRelations implements RelationInterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): array
    {
        $result = [];

        if ($value instanceof ObjectMetadata) {
            $value = $value->getObject();
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof ObjectMetadata) {
                    $v = $v->getObject();
                }

                $result[] = ['dest' => $v->getId(), 'type' => Service::getElementType($v)];
            }
        } elseif ($value instanceof ElementInterface) {
            $result[] = ['dest' => $value->getId(), 'type' => Service::getElementType($value)];
        }

        return $result;
    }
}
