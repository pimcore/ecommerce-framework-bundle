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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\OpenSearch\DefaultOpenSearch as ProductList;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;

class DefaultOpenSearch extends AbstractOpenSearch
{
    public function getProductList(): ProductListInterface
    {
        return new ProductList($this->tenantConfig);
    }
}
