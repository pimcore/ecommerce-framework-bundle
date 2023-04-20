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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\FilterService;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use Pimcore\Model\DataObject\Fieldcollection\Data\OrderByFields;
use Pimcore\Model\DataObject\FilterDefinition;

/**
 * Helper service class for setting up a product list utilizing the filter service
 * based on a filter definition and set filter parameters
 */
class ListHelper
{
    public function setupProductList(
        FilterDefinition $filterDefinition,
        ProductListInterface $productList,
        array &$params,
        FilterService $filterService,
        bool $loadFullPage,
        bool $excludeLimitOfFirstpage = false
    ): void {
        $orderByOptions = [];
        $orderKeysAsc = explode(',', ($filterDefinition->getOrderByAsc() ?? ''));
        foreach ($orderKeysAsc as $orderByEntry) {
            if (!empty($orderByEntry)) {
                $orderByOptions[$orderByEntry]['asc'] = true;
            }
        }

        $orderKeysDesc = explode(',', ($filterDefinition->getOrderByDesc() ?? ''));
        foreach ($orderKeysDesc as $orderByEntry) {
            if (!empty($orderByEntry)) {
                $orderByOptions[$orderByEntry]['desc'] = true;
            }
        }

        $offset = 0;

        $pageLimit = (int)($params['perPage'] ?? $filterDefinition->getPageLimit() ?? 50);

        $limitOnFirstLoad = (int)$filterDefinition->getLimitOnFirstLoad() ?: 6;

        if (isset($params['page'])) {
            $page = (int)$params['page'];
            $params['currentPage'] = $page;
            $offset = $pageLimit * ($page - 1);
        }

        $productList->setLimit($pageLimit);

        if ($filterDefinition->getAjaxReload()) {
            $productList->setLimit($limitOnFirstLoad);
            if ($loadFullPage) {
                $productList->setLimit($pageLimit);
                if ($excludeLimitOfFirstpage) {
                    $offset += $limitOnFirstLoad;
                    $productList->setLimit($pageLimit - $limitOnFirstLoad);
                }
            }
        }

        $productList->setOffset($offset);

        $params['pageLimit'] = $pageLimit;

        $orderByField = null;
        $orderByDirection = null;

        if (isset($params['orderBy'])) {
            $orderBy = explode('#', $params['orderBy']);
            $orderByField = $orderBy[0];

            if (count($orderBy) > 1) {
                $orderByDirection = $orderBy[1];
            }
        }

        if (array_key_exists($orderByField, $orderByOptions)) {
            $params['currentOrderBy'] = htmlentities($params['orderBy']);

            $productList->setOrderKey($orderByField);

            if ($orderByDirection) {
                $productList->setOrder($orderByDirection);
            }
        } else {
            $orderByCollection = $filterDefinition->getDefaultOrderBy();
            $orderByList = [];
            if ($orderByCollection) {
                /** @var OrderByFields $orderBy */
                foreach ($orderByCollection as $orderBy) {
                    if ($orderBy->getField()) {
                        $orderByList[] = [$orderBy->getField(), $orderBy->getDirection()];
                    }
                }

                $params['currentOrderBy'] = implode('#', reset($orderByList));
            }
            if ($orderByList) {
                $productList->setOrderKey($orderByList);
                $productList->setOrder('ASC');
            }
        }

        $params['currentFilter'] = $filterService->initFilterService($filterDefinition, $productList, $params);

        $params['orderByOptions'] = $orderByOptions;
    }

    public function createPagingQuerystring(int $page): string
    {
        $params = $_REQUEST;
        $params['page'] = $page;
        unset($params['fullpage']);

        $string = '?';
        foreach ($params as $k => $p) {
            if (is_array($p)) {
                foreach ($p as $subKey => $subValue) {
                    $string .= $k . '[' . $subKey . ']' . '=' . urlencode($subValue) . '&';
                }
            } else {
                $string .= $k . '=' . urlencode($p) . '&';
            }
        }

        return $string;
    }

    public function getFirstFilteredCategory(array $conditions): ?AbstractCategory
    {
        if (!empty($conditions)) {
            foreach ($conditions as $c) {
                if ($c instanceof \Pimcore\Model\DataObject\Fieldcollection\Data\FilterCategory) {
                    $result = $c->getPreSelect();
                    if ($result instanceof AbstractCategory) {
                        return $result;
                    }
                }
            }
        }

        return null;
    }
}
