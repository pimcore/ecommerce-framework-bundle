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

use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\Exception\FilterTypeNotFoundException;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinition;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;

class FilterService
{
    protected FilterGroupHelper $filterGroupHelper;

    /**
     * @var AbstractFilterType[]
     */
    protected array $filterTypes = [];

    /**
     * @param AbstractFilterType[] $filterTypes
     */
    public function __construct(FilterGroupHelper $filterGroupHelper, array $filterTypes)
    {
        $this->filterGroupHelper = $filterGroupHelper;

        foreach ($filterTypes as $name => $filterType) {
            $this->registerFilterType($name, $filterType);
        }
    }

    protected function registerFilterType(string $name, AbstractFilterType $filterType): void
    {
        $this->filterTypes[$name] = $filterType;
    }

    public function getFilterType(string $name): AbstractFilterType
    {
        if (!isset($this->filterTypes[$name])) {
            throw new FilterTypeNotFoundException(sprintf('Filter type "%s" is not registered', $name));
        }

        return $this->filterTypes[$name];
    }

    public function getFilterGroupHelper(): FilterGroupHelper
    {
        return $this->filterGroupHelper;
    }

    /**
     * Initializes the FilterService, adds all conditions to the ProductList and returns an array of the currently set
     * filters
     *
     * @param AbstractFilterDefinition $filterObject filter definition object to use
     * @param ProductListInterface $productList              product list to use and add conditions to
     * @param array $params                          request params with eventually set filter conditions
     *
     * @return array returns set filters
     */
    public function initFilterService(AbstractFilterDefinition $filterObject, ProductListInterface $productList, array $params = []): array
    {
        $currentFilter = [];

        if ($filterObject->getFilters()) {
            foreach ($filterObject->getFilters() as $filter) {
                $currentFilter = $this->addCondition($filter, $productList, $currentFilter, $params);
            }
            //do this in a separate loop in order to make sure that all filters are set when group by values are prepared
            foreach ($filterObject->getFilters() as $filter) {
                //prepare group by filters
                $this->getFilterType($filter->getType())->prepareGroupByValues($filter, $productList);
            }
        }

        if ($filterObject->getConditions()) {
            foreach ($filterObject->getConditions() as $condition) {
                $this->addCondition($condition, $productList, $currentFilter, [], true);
            }
        }

        return $currentFilter;
    }

    /**
     * Returns filter frontend script for given filter type (delegates)
     *
     * @param AbstractFilterDefinitionType $filterDefinition filter definition to get frontend script for
     * @param ProductListInterface $productList current product list (with all set filters) to get available options and counts
     * @param array $currentFilter current filter for this filter definition
     *
     * @return string view snippet
     */
    public function getFilterFrontend(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): string
    {
        return $this
            ->getFilterType($filterDefinition->getType())
            ->getFilterFrontend($filterDefinition, $productList, $currentFilter);
    }

    /**
     * Returns filter data for given filter type (delegates)
     *
     * @param AbstractFilterDefinitionType $filterDefinition filter definition to get frontend script for
     * @param ProductListInterface $productList current product list (with all set filters) to get available options and counts
     * @param array $currentFilter current filter for this filter definition
     *
     */
    public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array
    {
        return $this
            ->getFilterType($filterDefinition->getType())
            ->getFilterValues($filterDefinition, $productList, $currentFilter);
    }

    /**
     * Adds condition - delegates it to the AbstractFilterType instance
     *
     *
     * @return array updated currentFilter array
     */
    public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array
    {
        return $this
            ->getFilterType($filterDefinition->getType())
            ->addCondition($filterDefinition, $productList, $currentFilter, $params, $isPrecondition);
    }
}
