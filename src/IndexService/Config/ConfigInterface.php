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

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition\Attribute;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use Pimcore\Model\DataObject;

/**
 * Interface for IndexService Tenant Configurations
 */
interface ConfigInterface
{
    /**
     * returns tenant name
     *
     */
    public function getTenantName(): string;

    /**
     * Returns configured attributes for product index
     *
     * @return Attribute[]
     */
    public function getAttributes(): array;

    /**
     * Returns full text search index attribute names for product index
     *
     */
    public function getSearchAttributes(): array;

    /**
     * return all supported filter types for product index
     *
     */
    public function getFilterTypeConfig(): ?array;

    /**
     * returns if given product is active for this tenant
     *
     *
     */
    public function isActive(IndexableInterface $object): bool;

    /**
     * checks, if product should be in index for current tenant
     *
     *
     */
    public function inIndex(IndexableInterface $object): bool;

    /**
     * Returns categories for given object in context of the current tenant.
     * Possible hook to filter categories for specific tenants.
     *
     *
     * @return \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory[]
     */
    public function getCategories(IndexableInterface $object, int $subObjectId = null): array;

    /**
     * in case of subtenants returns a data structure containing all sub tenants
     *
     *
     * @return mixed $subTenantData
     */
    public function prepareSubTenantEntries(IndexableInterface $object, int $subObjectId = null): mixed;

    /**
     * populates index for tenant relations based on given data
     *
     *
     */
    public function updateSubTenantEntries(mixed $objectId, mixed $subTenantData, mixed $subObjectId = null): void;

    /**
     * Config <-> worker have a 1:1 relation as the config
     * needs to access its worker in certain cases.
     *
     *
     * @throws \LogicException If the config already has a worker set
     * @throws \LogicException If the config used from the worker does not match the config object the worker is
     *                         about to be set to
     */
    public function setTenantWorker(WorkerInterface $tenantWorker): void;

    /**
     * creates and returns tenant worker suitable for this tenant configuration
     *
     */
    public function getTenantWorker(): WorkerInterface;

    /**
     * creates an array of sub ids for the given object
     * use that function, if one object should be indexed more than once (e.g. if field collections are in use)
     *
     *
     * @return IndexableInterface[]
     */
    public function createSubIdsForObject(IndexableInterface $object): array;

    /**
     * checks if there are some zombie subIds around and returns them for cleanup
     *
     *
     */
    public function getSubIdsToCleanup(IndexableInterface $object, array $subIds): array;

    /**
     * creates virtual parent id for given sub id
     * default is getOSParentId
     *
     *
     */
    public function createVirtualParentIdForSubId(IndexableInterface $object, int $subId): mixed;

    /**
     * Gets object by id, can consider subIds and therefore return e.g. an array of values
     * always returns object itself - see also getObjectMockupById
     *
     * @param bool $onlyMainObject - only returns main object
     *
     */
    public function getObjectById(int $objectId, bool $onlyMainObject = false): ?DataObject;

    /**
     * Gets object mockup by id, can consider subIds and therefore return e.g. an array of values
     * always returns a object mockup if available
     *
     *
     */
    public function getObjectMockupById(int $objectId): ?IndexableInterface;

    /**
     * returns column type for id
     *
     *
     */
    public function getIdColumnType(bool $isPrimary): string;

    /**
     * Attribute configuration
     *
     */
    public function getAttributeConfig(): array;
}
