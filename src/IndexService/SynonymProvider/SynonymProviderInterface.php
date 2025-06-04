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

interface SynonymProviderInterface
{
    /**
     * Get synonyms, depending on the format that is specified in the filter.
     * Typically Solr is used, compare https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-synonym-tokenfilter.html.
     * Examples:
     *      - line 1: i-pod, i pod => ipod
     *      - line 2: sea biscuit, sea biscit => seabiscuit
     *      - ...
     *
     * @return string[] an array, where each array element corresponds to one line of related synonyms.
     */
    public function getSynonyms(): array;

    /**
     * return a list of options that can be configured per options provider and can be used for the
     * implementation of the synonym provider.
     *
     */
    public function getOptions(): array;
}
