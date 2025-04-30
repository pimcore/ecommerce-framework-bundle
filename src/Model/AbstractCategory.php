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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Model;

/**
 * Abstract base class for pimcore objects who should be used as product categories in the online shop framework
 */
class AbstractCategory extends \Pimcore\Model\DataObject\Concrete
{
    /**
     * defines if product is visible in product index queries for parent categories of product category.
     * e.g.
     *   football
     *     - shoes
     *     - shirts
     *
     * all products if category shoes or shirts are visible in queries for category football
     *
     */
    public function getOSProductsInParentCategoryVisible(): bool
    {
        return true;
    }
}
