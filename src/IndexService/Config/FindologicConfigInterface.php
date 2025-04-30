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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\DefaultFindologic as DefaultFindologicWorker;

/**
 * Interface for IndexService Tenant Configurations using findologic as index
 */
interface FindologicConfigInterface extends ConfigInterface
{
    /**
     * returns findologic client parameters defined in the tenant config
     *
     *
     */
    public function getClientConfig(?string $setting = null): array|string|null;

    /**
     * returns condition for current subtenant
     *
     */
    public function getSubTenantCondition(): array;

    /**
     * creates and returns tenant worker suitable for this tenant configuration
     *
     */
    public function getTenantWorker(): DefaultFindologicWorker;
}
