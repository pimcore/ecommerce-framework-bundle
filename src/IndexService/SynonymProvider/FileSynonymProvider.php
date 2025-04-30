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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider;

use Pimcore\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileSynonymProvider extends AbstractSynonymProvider implements SynonymProviderInterface
{
    use OptionsResolverTrait;

    const SYNONYM_FILE_OPTION = 'synonymFile';

    public function getSynonyms(): array
    {
        $options = $this->resolveOptions($this->getOptions());
        $filePath = $options[static::SYNONYM_FILE_OPTION];
        if (!file_exists($filePath)) {
            throw new \Exception(sprintf('File "%s" does not exist on the local system. Please verify the path.', $filePath));
        }

        $content = file_get_contents($filePath);
        $synonymLines = explode_and_trim(PHP_EOL, $content);

        return $synonymLines;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::SYNONYM_FILE_OPTION);
    }
}
