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

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdList implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): ?string
    {
        $config = $this->resolveOptions($config ?? []);

        $ids = [];

        if (is_array($value)) {
            foreach ($value as $val) {
                if ($val && method_exists($val, 'getId')) {
                    $ids[] = $val->getId();
                }
            }
        } elseif ($value && method_exists($value, 'getId')) {
            $ids[] = $value->getId();
        }

        $delimiter = ',';

        if ($config['multiSelectEncoded']) {
            $delimiter = WorkerInterface::MULTISELECT_DELIMITER;
        }

        $ids = implode($delimiter, $ids);

        return $ids ? $delimiter . $ids . $delimiter : null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('multiSelectEncoded', false)
            ->setAllowedTypes('multiSelectEncoded', 'bool');
    }
}
