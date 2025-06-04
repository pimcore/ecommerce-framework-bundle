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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager;

use ArrayAccess;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Pimcore\Model\Paginator\PaginateListingInterface;
use SeekableIterator;

/**
 * Interface OrderListInterface
 *
 * @method OrderListItemInterface|false current()
 */
interface OrderListInterface extends SeekableIterator, ArrayAccess, PaginateListingInterface
{
    const LIST_TYPE_ORDER = 'order';

    const LIST_TYPE_ORDER_ITEM = 'item';

    public function getQueryBuilder(): DoctrineQueryBuilder;

    public function load(): OrderListInterface;

    public function setLimit(int $limit, int $offset = 0): OrderListInterface;

    public function getLimit(): int;

    public function getOffset(): int;

    /**
     * @return $this
     */
    public function setOrder(string $order): static;

    /**
     * @return $this
     */
    public function setOrderState(string $state): static;

    public function getOrderState(): string;

    /**
     * @return $this
     */
    public function setListType(string $type): static;

    public function getListType(): string;

    public function getItemClassName(): string;

    /**
     * @return $this
     */
    public function setItemClassName(string $className): static;

    /**
     * enable payment info query
     * table alias: paymentInfo
     *
     * @return $this
     */
    public function joinPaymentInfo(): static;

    /**
     * enable order item objects query
     * table alias: orderItemObjects
     *
     * @return $this
     */
    public function joinOrderItemObjects(): static;

    /**
     * enable product query
     * table alias: product
     *
     *
     * @return $this
     */
    public function joinProduct(string $classId): static;

    /**
     * enable customer query
     * table alias: customer
     *
     *
     * @return $this
     */
    public function joinCustomer(string $classId): static;

    /**
     * enable pricing rule query
     * table alias: pricingRule
     *
     * @return $this
     */
    public function joinPricingRule(): static;

    /**
     *
     * @return $this
     */
    public function addCondition(string $condition, ?string $value = null): static;

    /**
     * @return $this
     */
    public function addSelectField(string $field): static;

    /**
     * @return $this
     */
    public function addFilter(OrderListFilterInterface $filter): static;

    public function useSubItems(): bool;

    /**
     * @return $this
     */
    public function setUseSubItems(bool $useSubItems): static;
}
