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
