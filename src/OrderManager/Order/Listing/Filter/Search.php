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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager\Order\Listing\Filter;

/**
 * Search filter with flexible column definition
 */
class Search extends AbstractSearch
{
    /**
     * Search column
     *
     */
    protected string $column;

    public function __construct(string $value, string $column)
    {
        parent::__construct($value);
        $this->column = $column;
    }

    protected function getConditionColumn(): string
    {
        return $this->column;
    }
}
