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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\OpenSearch;

use OpenSearch\Client;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\SearchConfigInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch\AbstractOpenSearch as Worker;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;

abstract class AbstractOpenSearch implements ProductListInterface
{
    const LIMIT_UNLIMITED = -1;

    const INTEGER_MAX_VALUE = 2147483647;     // Elasticsearch Integer.MAX_VALUE is 2^31-1

    const ADVANCED_SORT = 'advanced_sort';

    /**
     * @var null|IndexableInterface[]
     */
    protected ?array $products = null;

    /**
     * Timeout for a request in seconds
     *
     */
    protected int $timeout = 10;

    /**
     * Name of the index
     *
     */
    protected string $indexName = '';

    protected string $tenantName;

    protected SearchConfigInterface $tenantConfig;

    protected ?int $totalCount = null;

    protected string $variantMode = ProductListInterface::VARIANT_MODE_INCLUDE;

    protected ?int $limit = null;

    protected ?string $order = null;

    protected string|array $orderKey = '';

    protected bool $orderByPrice = false;

    protected int $offset = 0;

    protected ?AbstractCategory $category = null;

    protected bool $inProductList = false;

    protected array $filterConditions = [];

    protected array $queryConditions = [];

    protected array $relationConditions = [];

    protected ?float $conditionPriceFrom = null;

    protected ?float $conditionPriceTo = null;

    protected array $preparedGroupByValues = [];

    protected array $preparedGroupByValuesResults = [];

    protected bool $preparedGroupByValuesLoaded = false;

    protected array $searchAggregation = [];

    /**
     * contains a mapping from productId => array Index
     * useful when you have to merge child products to there parent and you don't want to iterate each time over the list
     *
     */
    protected array $productPositionMap = [];

    protected bool $doScrollRequest = false;

    protected string $scrollRequestKeepAlive = '30s';

    protected array $hitData = [];

    public function getSearchAggregation(): array
    {
        return $this->searchAggregation;
    }

    public function setSearchAggregation(array $searchAggregation): static
    {
        $this->searchAggregation = $searchAggregation;

        return $this;
    }

    public function __construct(SearchConfigInterface $tenantConfig)
    {
        $this->tenantName = $tenantConfig->getTenantName();
        $this->tenantConfig = $tenantConfig;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getProducts(): array
    {
        if ($this->products === null) {
            $this->load();
        }

        return $this->products;
    }

    /**
     * Returns the Mapping of the productId => position
     *
     */
    public function getProductPositionMap(): array
    {
        return $this->productPositionMap;
    }

    public function setProductPositionMap(array $productPositionMap): static
    {
        $this->productPositionMap = $productPositionMap;

        return $this;
    }

    /**
     * Adds condition to product list
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results
     *
     * @param string $fieldname - must be set for elastic search
     */
    public function addCondition(array|string $condition, string $fieldname = ''): void
    {
        $this->filterConditions[$fieldname][] = $condition;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    /**
     * Reset condition for fieldname
     *
     */
    public function resetCondition(string $fieldname): void
    {
        unset($this->filterConditions[$fieldname]);
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    /**
     * Adds relation condition to product list
     *
     */
    public function addRelationCondition(string $fieldname, string|array $condition): void
    {
        $this->relationConditions[$fieldname][] = $condition;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    /**
     * Resets all conditions of product list
     */
    public function resetConditions(): void
    {
        $this->relationConditions = [];
        $this->filterConditions = [];
        $this->queryConditions = [];
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    /**
     * Adds query condition to product list for fulltext search
     * Fieldname is optional but highly recommended - needed for resetting condition based on fieldname
     * and exclude functionality in group by results
     *
     * @param string $fieldname - must be set for elastic search
     */
    public function addQueryCondition(string|array $condition, string $fieldname = ''): void
    {
        $this->queryConditions[$fieldname][] = $condition;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    /**
     * Reset query condition for fieldname
     *
     */
    public function resetQueryCondition(string $fieldname): void
    {
        unset($this->queryConditions[$fieldname]);
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    /**
     * Adds price condition to product list
     *
     */
    public function addPriceCondition(float $from = null, float $to = null): void
    {
        $this->conditionPriceFrom = $from;
        $this->conditionPriceTo = $to;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    public function setInProductList(bool $inProductList): void
    {
        $this->inProductList = (bool) $inProductList;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    public function getInProductList(): bool
    {
        return $this->inProductList;
    }

    /**
     * sets order direction
     *
     *
     */
    public function setOrder(string $order): void
    {
        $this->order = strtolower($order);
        $this->products = null;
    }

    /**
     * gets order direction
     *
     */
    public function getOrder(): ?string
    {
        return $this->order;
    }

    /**
     * sets order key
     *
     * @param array|string $orderKey either:
     * Single field name
     * Array of field names
     * Array of arrays (field name, direction)
     * Array containing your sort configuration [self::ADVANCED_SORT => <sort_config as array>]
     *
     */
    public function setOrderKey(array|string $orderKey): void
    {
        $this->products = null;
        if ($orderKey == ProductListInterface::ORDERKEY_PRICE) {
            $this->orderByPrice = true;
        } else {
            $this->orderByPrice = false;
        }

        $this->orderKey = $orderKey;
    }

    public function getOrderKey(): array|string
    {
        return $this->orderKey;
    }

    /**
     * Pass -1 to enable the unlimited scroll request
     *
     *
     */
    public function setLimit(int $limit): void
    {
        if ($this->limit !== $limit) {
            $this->products = null;
        }

        if ($limit === static::LIMIT_UNLIMITED) {
            $this->limit = 100;
            $this->doScrollRequest = true;
        } else {
            $this->doScrollRequest = false;
            $this->limit = $limit;
        }
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setOffset(int $offset): void
    {
        if ($this->offset != $offset) {
            $this->products = null;
        }
        $this->offset = $offset;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setCategory(AbstractCategory $category): void
    {
        $this->category = $category;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    public function getCategory(): ?AbstractCategory
    {
        return $this->category;
    }

    public function setVariantMode(string $variantMode): void
    {
        $this->variantMode = $variantMode;
        $this->preparedGroupByValuesLoaded = false;
        $this->products = null;
    }

    public function getVariantMode(): string
    {
        return $this->variantMode;
    }

    /**
     * loads search results from index and returns them
     *
     * @return IndexableInterface[]
     *
     * @throws \Exception
     */
    public function load(): array
    {
        $objectRaws = [];

        //First case: no price filtering and no price sorting
        if (!$this->orderByPrice && $this->conditionPriceFrom === null && $this->conditionPriceTo === null) {
            $objectRaws = $this->loadWithoutPriceFilterWithoutPriceSorting();
        }

        //Second case: no price filtering but price sorting
        elseif ($this->orderByPrice && $this->conditionPriceFrom === null && $this->conditionPriceTo === null) {
            $objectRaws = $this->loadWithoutPriceFilterWithPriceSorting();
        }

        //Third case: price filtering but no price sorting
        elseif (!$this->orderByPrice && ($this->conditionPriceFrom !== null || $this->conditionPriceTo !== null)) {
            $objectRaws = $this->loadWithPriceFilterWithoutPriceSorting();
        }

        //Forth case: price filtering and price sorting
        elseif ($this->orderByPrice && ($this->conditionPriceFrom !== null || $this->conditionPriceTo !== null)) {
            $objectRaws = $this->loadWithPriceFilterWithPriceSorting();
        }

        // load elements
        $this->products = $this->productPositionMap = [];
        $i = 0;
        foreach ($objectRaws as $raw) {
            $product = $this->loadElementById((int) $raw);
            if ($product) {
                $this->products[] = $product;
                $this->productPositionMap[$product->getId()] = $i;
                $i++;
            }
        }

        return $this->products;
    }

    /**
     * Returns the Elasticsearch query parameters
     *
     */
    public function getQuery(): array
    {
        $boolFilters = [];
        $queryFilters = [];

        //pre conditions
        $boolFilters = $this->buildSystemConditions($boolFilters);

        //user specific filters
        $boolFilters = $this->buildFilterConditions($boolFilters, []);

        //relation conditions
        $boolFilters = $this->buildRelationConditions($boolFilters, []);

        //query conditions
        $queryFilters = $this->buildQueryConditions($queryFilters, []);

        $params = [];
        $params['index'] = $this->getIndexName();
        $params['track_total_hits'] = true;
        $params['rest_total_hits_as_int'] = true;

        $params['body']['_source'] = true;

        if (is_int($this->getLimit())) { // null not allowed
            $params['body']['size'] = $this->getLimit();
        }
        $params['body']['from'] = $this->getOffset();

        if ($this->orderKey) {
            if (is_array($this->orderKey)) {
                if (!empty($this->orderKey[self::ADVANCED_SORT])) {
                    $params['body']['sort'] = $this->orderKey[self::ADVANCED_SORT];
                } else {
                    foreach ($this->orderKey as $orderKey) {
                        $params['body']['sort'][] = [$this->tenantConfig->getFieldNameMapped($orderKey[0]) => (strtolower($orderKey[1]) ?: 'asc')];
                    }
                }
            } else {
                $params['body']['sort'][] = [$this->tenantConfig->getFieldNameMapped($this->orderKey) => ($this->order ?: 'asc')];
            }
        }

        if ($aggs = $this->getSearchAggregation()) {
            foreach ($aggs as $name => $type) {
                $params['body']['aggs'][$name] = $type;
            }
        }

        // build query for request
        $params = $this->buildQuery($params, $boolFilters, $queryFilters);

        return $params;
    }

    /**
     * First case: no price filtering and no price sorting
     *
     */
    protected function loadWithoutPriceFilterWithoutPriceSorting(): array
    {
        $params = $this->getQuery();

        $this->hitData = [];

        // send request
        $result = $this->sendRequest($params);

        $objectRaws = [];
        if ($result['hits'] ?? null) {
            $this->totalCount = $result['hits']['total'];
            foreach ($result['hits']['hits'] as $hit) {
                $objectRaws[] = $hit['_id'];
                $this->hitData[$hit['_id']] = $hit;
            }
        }

        return $objectRaws;
    }

    /**
     * Second case: no price filtering but price sorting
     *
     *
     * @throws \Exception
     */
    protected function loadWithoutPriceFilterWithPriceSorting(): array
    {
        $params = $this->getQuery();
        $this->hitData = [];

        unset($params['body']['sort']);     // don't send the sort parameter, because it doesn't exist with offline sorting
        $params['body']['size'] = 10000;    // won't work with more than 10000 items in the result (opensearch limit)
        $params['body']['from'] = 0;
        $result = $this->sendRequest($params);
        $objectRaws = [];
        if ($result['hits']) {
            $this->totalCount = $result['hits']['total'];
            foreach ($result['hits']['hits'] as $hit) {
                $objectRaws[] = ['id' => $hit['_id'], 'priceSystemName' => $hit['_source']['system']['priceSystemName']];
                $this->hitData[$hit['_id']] = $hit;
            }
        }
        $priceSystemArrays = [];
        foreach ($objectRaws as $raw) {
            $priceSystemArrays[$raw['priceSystemName']][] = $raw['id'];
        }
        if (count($priceSystemArrays) == 1) {
            $priceSystemName = key($priceSystemArrays);
            $priceSystem = Factory::getInstance()->getPriceSystem($priceSystemName);
            $objectRaws = $priceSystem->filterProductIds($priceSystemArrays[$priceSystemName], null, null, $this->order, $this->getOffset(), $this->getLimit());
        } elseif (count($priceSystemArrays) == 0) {
            //nothing to do
        } else {
            throw new \Exception('Not implemented yet - multiple pricing systems are not supported yet');
        }

        $raws = [];

        foreach ($objectRaws as $raw) {
            $raws[] = $raw['id'];
        }

        return $raws;
    }

    /**
     * Third case: price filtering but no price sorting
     *
     *
     * @throws \Exception
     */
    protected function loadWithPriceFilterWithoutPriceSorting(): array
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * Forth case: price filtering and price sorting
     *
     *
     * @throws \Exception
     */
    protected function loadWithPriceFilterWithPriceSorting(): array
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * build the complete query
     *
     *
     */
    protected function buildQuery(array $params, array $boolFilters, array $queryFilters, string $variantMode = null): array
    {
        if (!$variantMode) {
            $variantMode = $this->getVariantMode();
        }

        if ($variantMode == ProductListInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            $params['body']['query']['bool']['must']['has_child']['type'] = self::PRODUCT_TYPE_VARIANT;
            $params['body']['query']['bool']['must']['has_child']['score_mode'] = 'avg';
            $params['body']['query']['bool']['must']['has_child']['query']['bool']['must'] = $queryFilters;
            $params['body']['query']['bool']['must']['has_child']['query']['bool']['filter']['bool']['must'] = $boolFilters;

            //add matching variant Ids to the result
            $params['body']['query']['bool']['must']['has_child']['inner_hits'] = [
                'name' => 'variants',
                '_source' => false,
                'size' => 100,
            ];
        } else {
            if ($variantMode == ProductListInterface::VARIANT_MODE_VARIANTS_ONLY) {
                $boolFilters[] = [
                    'term' => ['type' => self::PRODUCT_TYPE_VARIANT],
                ];
            } elseif ($variantMode == ProductListInterface::VARIANT_MODE_HIDE) {
                $boolFilters[] = [
                    'term' => ['type' => self::PRODUCT_TYPE_OBJECT],
                ];
            }

            $params['body']['query']['bool']['must']['bool']['must'] = $queryFilters;
            $params['body']['query']['bool']['filter']['bool']['must'] = $boolFilters;
        }

        return $params;
    }

    /**
     * builds system conditions
     *
     *
     */
    protected function buildSystemConditions(array $boolFilters): array
    {
        $boolFilters[] = ['term' => ['system.active' => true]];
        $boolFilters[] = ['term' => ['system.virtualProductActive' => true]];
        if ($this->inProductList) {
            $boolFilters[] = ['term' => ['system.inProductList' => true]];
        }

        $tenantCondition = $this->tenantConfig->getSubTenantCondition();
        if ($tenantCondition) {
            $boolFilters[] = $tenantCondition;
        }

        if ($this->getCategory()) {
            $boolFilters[] = ['term' => ['system.parentCategoryIds' => $this->getCategory()->getId()]];
        }

        return $boolFilters;
    }

    /**
     * builds relation conditions of user specific query conditions
     *
     *
     */
    protected function buildRelationConditions(array $boolFilters, array $excludedFieldnames): array
    {
        foreach ($this->relationConditions as $fieldname => $relationConditionArray) {
            if (!array_key_exists($fieldname, $excludedFieldnames)) {
                foreach ($relationConditionArray as $relationCondition) {
                    if (is_array($relationCondition)) {
                        $boolFilters[] = $relationCondition;
                    } else {
                        $boolFilters[] = ['term' => [$this->tenantConfig->getFieldNameMapped($fieldname) => $relationCondition]];
                    }
                }
            }
        }

        return $boolFilters;
    }

    /**
     * builds filter condition of user specific conditions
     *
     *
     */
    protected function buildFilterConditions(array $boolFilters, array $excludedFieldnames): array
    {
        foreach ($this->filterConditions as $fieldname => $filterConditionArray) {
            if (!array_key_exists($fieldname, $excludedFieldnames)) {
                foreach ($filterConditionArray as $filterCondition) {
                    if (is_array($filterCondition)) {
                        $boolFilters[] = $filterCondition;
                    } else {
                        $boolFilters[] = ['term' => [$this->tenantConfig->getFieldNameMapped($fieldname, true) => $filterCondition]];
                    }
                }
            }
        }

        return $boolFilters;
    }

    /**
     * builds query condition of query filters
     *
     *
     */
    protected function buildQueryConditions(array $queryFilters, array $excludedFieldnames): array
    {
        foreach ($this->queryConditions as $fieldname => $queryConditionArray) {
            if (!array_key_exists($fieldname, $excludedFieldnames)) {
                foreach ($queryConditionArray as $queryCondition) {
                    if (is_array($queryCondition)) {
                        $queryFilters[] = $queryCondition;
                    } else {
                        if ($fieldname) {
                            $queryFilters[] = ['match' => [$this->tenantConfig->getFieldNameMapped($fieldname) => $queryCondition]];
                        } else {
                            $fieldnames = $this->tenantConfig->getSearchAttributes();
                            $mappedFieldnames = [];
                            foreach ($fieldnames as $searchFieldnames) {
                                $mappedFieldnames[] = $this->tenantConfig->getFieldNameMapped($searchFieldnames, true);
                            }

                            $queryFilters[] = ['multi_match' => [
                                'query' => $queryCondition,
                                'fields' => $mappedFieldnames,
                            ]];
                        }
                    }
                }
            }
        }

        return $queryFilters;
    }

    /**
     * loads element by id
     *
     *
     */
    protected function loadElementById(int $elementId): ?\Pimcore\Bundle\EcommerceFrameworkBundle\Model\DefaultMockup
    {
        /** @var ElasticSearch $tenantConfig */
        $tenantConfig = $this->getTenantConfig();
        $mockup = null;
        if (isset($this->hitData[$elementId])) {
            $hitData = $this->hitData[$elementId];
            $sourceData = $hitData['_source'];

            //mapping of relations
            $relationFormatPimcore = [];
            foreach ($sourceData['relations'] ?? [] as $name => $relation) {
                $relationFormatPimcore[] = ['fieldname' => $name, 'dest' => $relation[0], 'type' => 'object'];
            }
            $mergedAttributes = array_merge($sourceData['system'], $sourceData['attributes']);
            $mockup = $tenantConfig->createMockupObject($elementId, $mergedAttributes, $relationFormatPimcore);
        }

        return $mockup;
    }

    /**
     * prepares all group by values for given field names and cache them in local variable
     * considers both - normal values and relation values
     *
     *
     */
    public function prepareGroupByValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): void
    {
        if ($fieldname) {
            $this->preparedGroupByValues[$this->tenantConfig->getFieldNameMapped($fieldname, true)] = ['countValues' => $countValues, 'fieldnameShouldBeExcluded' => $fieldnameShouldBeExcluded];
            $this->preparedGroupByValuesLoaded = false;
        }
    }

    /**
     *
     * @throws \Exception
     */
    public function prepareGroupByValuesWithConfig(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true, array $aggregationConfig = []): void
    {
        if ($this->getVariantMode() == ProductListInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            throw new \Exception('Custom sub aggregations are not supported for variant mode VARIANT_MODE_INCLUDE_PARENT_OBJECT');
        }

        if ($fieldname) {
            $this->preparedGroupByValues[$this->tenantConfig->getFieldNameMapped($fieldname, true)] = [
                'countValues' => $countValues,
                'fieldnameShouldBeExcluded' => $fieldnameShouldBeExcluded,
                'aggregationConfig' => $aggregationConfig,
            ];
            $this->preparedGroupByValuesLoaded = false;
        }
    }

    /**
     * prepares all group by values for given field names and cache them in local variable
     * considers both - normal values and relation values
     *
     *
     */
    public function prepareGroupByRelationValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): void
    {
        if ($fieldname) {
            $this->preparedGroupByValues[$this->tenantConfig->getFieldNameMapped($fieldname, true)] = ['countValues' => $countValues, 'fieldnameShouldBeExcluded' => $fieldnameShouldBeExcluded];
            $this->preparedGroupByValuesLoaded = false;
        }
    }

    /**
     * prepares all group by values for given field names and cache them in local variable
     * considers both - normal values and relation values
     *
     *
     */
    public function prepareGroupBySystemValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): void
    {
        $this->preparedGroupByValues[$this->tenantConfig->getFieldNameMapped($fieldname)] = ['countValues' => $countValues, 'fieldnameShouldBeExcluded' => $fieldnameShouldBeExcluded];
        $this->preparedGroupByValuesLoaded = false;
    }

    /**
     * resets all set prepared group by values
     *
     */
    public function resetPreparedGroupByValues(): void
    {
        $this->preparedGroupByValuesLoaded = false;
        $this->preparedGroupByValues = [];
        $this->preparedGroupByValuesResults = [];
    }

    /**
     * loads group by values based on system either from local variable if prepared or directly from product index
     *
     * @param bool $fieldnameShouldBeExcluded => set to false for and-conditions
     *
     * @throws \Exception
     */
    public function getGroupBySystemValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): array
    {
        return $this->doGetGroupByValues($this->tenantConfig->getFieldNameMapped($fieldname), $countValues, $fieldnameShouldBeExcluded);
    }

    /**
     * loads group by values based on fieldname either from local variable if prepared or directly from product index
     *
     * @param bool $fieldnameShouldBeExcluded => set to false for and-conditions
     *
     * @throws \Exception
     */
    public function getGroupByValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): array
    {
        return $this->doGetGroupByValues($this->tenantConfig->getFieldNameMapped($fieldname, true), $countValues, $fieldnameShouldBeExcluded);
    }

    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index
     *
     * @param bool $fieldnameShouldBeExcluded => set to false for and-conditions
     *
     * @throws \Exception
     */
    public function getGroupByRelationValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): array
    {
        return $this->doGetGroupByValues($this->tenantConfig->getFieldNameMapped($fieldname, true), $countValues, $fieldnameShouldBeExcluded);
    }

    /**
     * checks if group by values are loaded and returns them
     *
     *
     */
    protected function doGetGroupByValues(string $fieldname, bool $countValues = false, bool $fieldnameShouldBeExcluded = true): array
    {
        if (!$this->preparedGroupByValuesLoaded) {
            $this->doLoadGroupByValues();
        }

        $results = $this->preparedGroupByValuesResults[$fieldname] ?? null;
        if ($results) {
            if ($countValues) {
                return $results;
            } else {
                $resultsWithoutCounts = [];
                foreach ($results as $result) {
                    $resultsWithoutCounts[] = $result['value'];
                }

                return $resultsWithoutCounts;
            }
        } else {
            return [];
        }
    }

    /**
     * loads all prepared group by values
     *   1 - get general filter (= filter of fields don't need to be considered in group by values or where fieldnameShouldBeExcluded set to false)
     *   2 - for each group by value create a own aggregation section with all other group by filters added
     *
     * @throws \Exception
     */
    protected function doLoadGroupByValues(): void
    {
        // create general filters and queries
        $toExcludeFieldnames = [];
        /** @var ElasticSearch $tenantConfig */
        $tenantConfig = $this->getTenantConfig();
        foreach ($this->preparedGroupByValues as $fieldname => $config) {
            if ($config['fieldnameShouldBeExcluded']) {
                $toExcludeFieldnames[$tenantConfig->getReverseMappedFieldName($fieldname)] = $fieldname;
            }
        }

        $boolFilters = [];
        $queryFilters = [];

        //pre conditions
        $boolFilters = $this->buildSystemConditions($boolFilters);

        //user specific filters
        $boolFilters = $this->buildFilterConditions($boolFilters, $toExcludeFieldnames);

        //relation conditions
        $boolFilters = $this->buildRelationConditions($boolFilters, $toExcludeFieldnames);

        //query conditions
        $queryFilters = $this->buildQueryConditions($queryFilters, []);

        $aggregations = [];

        //calculate already filtered attributes
        $filteredFieldnames = [];
        foreach ($this->filterConditions as $fieldname => $condition) {
            if (!array_key_exists($fieldname, $toExcludeFieldnames)) {
                $filteredFieldnames[$fieldname] = $fieldname;
            }
        }
        foreach ($this->relationConditions as $fieldname => $condition) {
            if (!array_key_exists($fieldname, $toExcludeFieldnames)) {
                $filteredFieldnames[$fieldname] = $fieldname;
            }
        }

        foreach ($this->preparedGroupByValues as $fieldname => $config) {
            //exclude all attributes that are already filtered
            $shortFieldname = $this->getTenantConfig()->getReverseMappedFieldName($fieldname);

            $specificFilters = [];
            //user specific filters
            $specificFilters = $this->buildFilterConditions($specificFilters, array_merge($filteredFieldnames, [$shortFieldname => $shortFieldname]));
            //relation conditions
            $specificFilters = $this->buildRelationConditions($specificFilters, array_merge($filteredFieldnames, [$shortFieldname => $shortFieldname]));

            if (!empty($config['aggregationConfig'])) {
                $aggregation = $config['aggregationConfig'];
            } else {
                $aggregation = [
                    'terms' => ['field' => $fieldname, 'size' => self::INTEGER_MAX_VALUE, 'order' => ['_key' => 'asc']],
                ];
            }

            if ($specificFilters) {
                $aggregations[$fieldname] = [
                    'filter' => [
                        'bool' => [
                            'must' => $specificFilters,
                        ],
                    ],
                    'aggs' => [
                        $fieldname => $aggregation,
                    ],
                ];

                //necessary to calculate correct counts of search results for filter values
                if ($this->getVariantMode() == ProductListInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                    $aggregations[$fieldname]['aggs'][$fieldname]['aggs'] = [
                        'objectCount' => ['cardinality' => ['field' => 'system.virtualProductId']],
                    ];
                }
            } else {
                $aggregations[$fieldname] = $aggregation;

                //necessary to calculate correct counts of search results for filter values
                if ($this->getVariantMode() == ProductListInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                    $aggregations[$fieldname]['aggs'] = [
                        'objectCount' => ['cardinality' => ['field' => 'system.virtualProductId']],
                    ];
                }
            }
        }

        if ($aggregations) {
            $params = [];
            $params['index'] = $this->getIndexName();
            $params['body']['_source'] = false;
            $params['body']['size'] = 0;
            $params['body']['from'] = $this->getOffset();
            $params['body']['aggs'] = $aggregations;

            // build query for request
            $variantModeForAggregations = $this->getVariantMode();
            if ($this->getVariantMode() == ProductListInterface::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                $variantModeForAggregations = ProductListInterface::VARIANT_MODE_VARIANTS_ONLY;
            }

            // build query for request
            $params = $this->buildQuery($params, $boolFilters, $queryFilters, $variantModeForAggregations);

            // send request
            $result = $this->sendRequest($params);

            // process result from elasticsearch
            $this->processResult($result);
        } else {
            $this->preparedGroupByValuesResults = [];
        }

        $this->preparedGroupByValuesLoaded = true;
    }

    /**
     * process the result array from elasticsearch
     *
     *
     */
    protected function processResult(array $result): void
    {
        if ($result['aggregations']) {
            foreach ($result['aggregations'] as $fieldname => $aggregation) {
                $buckets = $this->searchForBuckets($aggregation);

                $groupByValueResult = [];
                if ($buckets) {
                    foreach ($buckets as $bucket) {
                        if ($this->getVariantMode() == self::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                            $groupByValueResult[] = ['value' => $bucket['key'], 'count' => $bucket['objectCount']['value']];
                        } else {
                            $data = $this->convertBucketValues($bucket);
                            $groupByValueResult[] = $data;
                        }
                    }
                }

                $this->preparedGroupByValuesResults[$fieldname] = $groupByValueResult;
            }
        }
    }

    /**
     * Deep search for buckets in result aggregations array, as the structure of the result array
     * may differ dependent on the used aggregations (i.e. date filters, nested aggr, ...)
     *
     *
     */
    protected function searchForBuckets(array $aggregations): array
    {
        if (array_key_exists('buckets', $aggregations)) {
            return $aggregations['buckets'];
        }

        // usually the relevant key is at the very end of the array so we reverse the order
        $aggregations = array_reverse($aggregations, true);

        foreach ($aggregations as $aggregation) {
            if (!is_array($aggregation)) {
                continue;
            }
            $buckets = $this->searchForBuckets($aggregation);
            if (!empty($buckets)) {
                return $buckets;
            }
        }

        return [];
    }

    /**
     * Recursively convert aggregation data (sub-aggregations possible)
     *
     *
     */
    protected function convertBucketValues(array $bucket): array
    {
        $data = [
            'value' => $bucket['key'] ?? null,
            'count' => $bucket['doc_count'],
        ];

        unset($bucket['key']);
        unset($bucket['doc_count']);

        if (!empty($bucket)) {
            $subAggregationField = array_key_first($bucket);
            $subAggregationBuckets = $bucket[$subAggregationField];
            $reverseAggregationField = array_key_last($bucket);
            $reverseAggregationBucket = $bucket[$reverseAggregationField];

            if (array_key_exists('key_as_string', $bucket)) {          // date aggregations
                $data['key_as_string'] = $bucket['key_as_string'];
            } elseif (is_array($reverseAggregationBucket) && array_key_exists('doc_count', $reverseAggregationBucket)) { // reverse aggregation
                $data['reverse_count'] = $reverseAggregationBucket['doc_count'];
            } elseif (is_array($subAggregationBuckets) && isset($subAggregationBuckets['buckets'])) {        // sub aggregations
                foreach ($subAggregationBuckets['buckets'] as $bucket) {
                    $data[$subAggregationField][] = $this->convertBucketValues($bucket);
                }
            }
        }

        return $data;
    }

    public function getTenantConfig(): SearchConfigInterface
    {
        return $this->tenantConfig;
    }

    /**
     * send a request to elasticsearch
     */
    protected function sendRequest(array $params): array
    {
        $worker = $this->tenantConfig->getTenantWorker();
        if (!$worker instanceof Worker) {
            throw new InvalidConfigException('Invalid worker configured, AbstractOpenSearch compatible worker expected.');
        }

        /**
         * @var Client $osClient
         */
        $osClient = $worker->getOpenSearchClient();
        $result = [];

        if ($osClient instanceof Client) {
            if ($this->doScrollRequest) {
                $params = array_merge(['scroll' => $this->scrollRequestKeepAlive], $params);
                //kind of dirty hack :/
                $params['body']['size'] = $this->getLimit();
            }

            $result = $osClient->search($params);

            if ($this->doScrollRequest) {
                $additionalHits = [];
                $scrollId = $result['_scroll_id'];

                while (true) {
                    $additionalResult = $osClient->scroll(['scroll_id' => $scrollId, 'scroll' => $this->scrollRequestKeepAlive]);

                    if (count($additionalResult['hits']['hits'])) {
                        $additionalHits = array_merge($additionalHits, $additionalResult['hits']['hits']);
                        $scrollId = $additionalResult['_scroll_id'];
                    } else {
                        break;
                    }
                }
                $result['hits']['hits'] = array_merge($result['hits']['hits'], $additionalHits);
            }
        }

        return $result;
    }

    protected function getIndexName(): string
    {
        if (!$this->indexName) {
            $this->indexName = ($this->tenantConfig->getClientConfig('indexName')) ? strtolower($this->tenantConfig->getClientConfig('indexName')) : strtolower($this->tenantConfig->getTenantName());
        }

        return $this->indexName;
    }

    /**
     *  -----------------------------------------------------------------------------------------
     *   Methods for Iterator
     *  -----------------------------------------------------------------------------------------
     */
    public function count(): int
    {
        $this->getProducts();

        return $this->totalCount ?? 0;
    }

    /**
     * @return IndexableInterface|false
     */
    public function current(): bool|IndexableInterface
    {
        $this->getProducts();

        return current($this->products);
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param int $offset Page offset
     * @param int $itemCountPerPage Number of items per page
     *
     */
    public function getItems(int $offset, int $itemCountPerPage): array
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->getProducts();
    }

    public function key(): ?int
    {
        $this->getProducts();

        return key($this->products);
    }

    public function next(): void
    {
        $this->getProducts();
        next($this->products);
    }

    public function rewind(): void
    {
        $this->getProducts();
        reset($this->products);
    }

    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * Get the score from a loaded product list based on a (Pimcore) product Id.
     *
     * @param int $productId the Pimcore product Id.
     *
     * @return float the score returned by Elastic Search.
     *
     * @throws \Exception if loadFromSource mode is not true.
     */
    public function getScoreFromLoadedList(int $productId): float
    {
        if (isset($this->hitData[$productId])) {
            return $this->hitData[$productId]['_score'];
        }

        return 0.0;
    }
}
