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
use Symfony\Component\OptionsResolver\OptionsResolver;

class StructuredTable implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): ?string
    {
        $config = $this->resolveOptions($config ?? []);

        $getter = 'get' . ucfirst($config['tablerow']) . '__' . ucfirst($config['tablecolumn']);

        if ($value && $value instanceof \Pimcore\Model\DataObject\Data\StructuredTable) {
            if (isset($config['defaultUnit'])) {
                return $value->$getter() . ' ' . $config['defaultUnit'];
            } else {
                return $value->$getter();
            }
        }

        return null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver->setDefined('defaultUnit');

        foreach (['tablerow', 'tablecolumn'] as $field) {
            $resolver
                ->setDefined($field)
                ->setAllowedTypes($field, ['string', 'int']); // TODO does int make sense?
        }
    }
}
