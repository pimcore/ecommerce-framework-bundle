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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter;

use Pimcore\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultBrickGetter implements GetterInterface
{
    use OptionsResolverTrait;

    public function get(object $object, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);

        $brickContainerGetter = 'get' . ucfirst($config['brickfield']);
        $brickContainer = $object->$brickContainerGetter();

        $brickGetter = 'get' . ucfirst($config['bricktype']);
        $brick = $brickContainer->$brickGetter();
        if ($brick) {
            $fieldGetter = 'get' . ucfirst($config['fieldname']);

            return $brick->$fieldGetter();
        }

        return null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        static::setupBrickGetterOptionsResolver($resolver);
    }

    public static function setupBrickGetterOptionsResolver(OptionsResolver $resolver): void
    {
        foreach (['brickfield', 'bricktype', 'fieldname'] as $field) {
            $resolver->setRequired($field);
            $resolver->setAllowedTypes($field, 'string');
        }
    }
}
