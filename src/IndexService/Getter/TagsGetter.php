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
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Tag;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsGetter implements GetterInterface
{
    use OptionsResolverTrait;

    public function get(object $object, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);

        $type = 'object';
        if ($object instanceof Asset) {
            $type = 'asset';
        } elseif ($object instanceof Document) {
            $type = 'document';
        }

        $tags = Tag::getTagsForElement($type, $object->getId());

        if (!$config['includeParentTags']) {
            return $tags;
        }

        $result = [];
        foreach ($tags as $tag) {
            $result[] = $tag;

            $parent = $tag->getParent();
            while ($parent instanceof Tag) {
                $result[] = $parent;
                $parent = $parent->getParent();
            }
        }

        return $result;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'includeParentTags' => false,
        ]);

        $resolver->setAllowedTypes('includeParentTags', 'bool');
    }
}
