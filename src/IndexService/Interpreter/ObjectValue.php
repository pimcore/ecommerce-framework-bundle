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

use Pimcore\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectValue implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);
        $targetList = $this->resolveOptions($config['target'], 'target');

        if ($value instanceof AbstractObject) {
            $fieldGetter = 'get' . ucfirst($targetList['fieldname']);

            if (method_exists($value, $fieldGetter)) {
                return $value->$fieldGetter($targetList['locale']);
            }
        }

        return null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        if ('default' === $resolverName) {
            $resolver
                ->setDefined('target')
                ->setAllowedTypes('target', 'array');
        } elseif ('target' === $resolverName) {
            $fields = ['fieldname', 'locale'];

            $resolver->setRequired($fields);
            foreach ($fields as $field) {
                $resolver->setAllowedTypes($field, 'string');
            }
        } else {
            throw new \InvalidArgumentException(sprintf('Resolver with name "%s" is not defined', $resolverName));
        }
    }
}
