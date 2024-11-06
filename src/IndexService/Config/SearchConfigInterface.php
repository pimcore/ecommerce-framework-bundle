<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider\SynonymProviderInterface;

/**
 * Interface for IndexService Tenant Configurations using a search server e.g. elastic or open search as index
 */
interface SearchConfigInterface extends ConfigInterface
{
    /**
     * returns condition for current subtenant
     */
    public function getSubTenantCondition(): array;

    /**
     * Get an associative array of configured synonym providers.
     *  - key: the name of the synonym provider configuration, which is equivalent to the name of the configured filter
     *  - value: the synonym provider
     *
     * @return SynonymProviderInterface[]
     */
    public function getSynonymProviders(): array;

    public function getClientConfig(string $property = null): array|string|null;

    /**
     * returns the full field name
     *
     * @param bool $considerSubFieldNames - activate to consider subfield names like name.analyzed or score definitions like name^3
     *
     */
    public function getFieldNameMapped(string $fieldName, bool $considerSubFieldNames = false): string;

    /**
     * returns short field name based on full field name
     * also considers subfield names like name.analyzed etc.
     */
    public function getReverseMappedFieldName(string $fullFieldName): bool|int|string;
}
