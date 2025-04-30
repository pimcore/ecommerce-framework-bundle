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

class DefaultBrickGetterSequence implements GetterInterface
{
    use OptionsResolverTrait;

    public function get(object $object, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);
        $sourceList = $config['source'];

        // normalize single entry to list
        if (isset($sourceList['brickfield'])) {
            $sourceList = [$sourceList];
        }

        foreach ($sourceList as $source) {
            $source = $this->resolveOptions((array)$source, 'source');

            $brickContainerGetter = 'get' . ucfirst($source['brickfield']);

            if (method_exists($object, $brickContainerGetter)) {
                $brickContainer = $object->$brickContainerGetter();

                $brickGetter = 'get' . ucfirst($source['bricktype']);
                $brick = $brickContainer->$brickGetter();
                if ($brick) {
                    $fieldGetter = 'get' . ucfirst($source['fieldname']);
                    $value = $brick->$fieldGetter();
                    if ($value) {
                        return $value;
                    }
                }
            }
        }

        return null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        if ('default' === $resolverName) {
            $resolver->setRequired('source');
            $resolver->setAllowedTypes('source', 'array');
        } elseif ('source' === $resolverName) {
            // brickfield, bricktype, fieldname
            DefaultBrickGetter::setupBrickGetterOptionsResolver($resolver);
        } else {
            throw new \InvalidArgumentException(sprintf('Resolver with name "%s" is not defined', $resolverName));
        }
    }
}
