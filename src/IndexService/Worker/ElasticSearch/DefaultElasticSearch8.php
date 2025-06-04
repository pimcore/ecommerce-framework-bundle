<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\ElasticSearch;

/**
 *  Use this for ES Version = 8
 */
class DefaultElasticSearch8 extends AbstractElasticSearch
{
    public function getProductList(): \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\DefaultElasticSearch8
    {
        return new \Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ElasticSearch\DefaultElasticSearch8($this->tenantConfig);
    }
}
