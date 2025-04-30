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

/**
 * Interface for IndexService Tenant Configurations using mysql as index
 */
interface MysqlConfigInterface extends ConfigInterface
{
    /**
     * returns table name of product index
     *
     */
    public function getTablename(): string;

    /**
     * returns table name of product index reations
     *
     */
    public function getRelationTablename(): string;

    /**
     * return table name of product index tenant relations for subtenants
     *
     */
    public function getTenantRelationTablename(): string;

    /**
     * return join statement in case of subtenants
     *
     */
    public function getJoins(): string;

    /**
     * returns additional condition in case of subtenants
     *
     */
    public function getCondition(): string;
}
