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
 * Abstract base class for filter definition type field collections for category filter
 */
abstract class CategoryFilterDefinitionType extends AbstractFilterDefinitionType
{
    public function getField(): string
    {
        if ($this->getIncludeParentCategories()) {
            return 'parentCategoryIds';
        } else {
            return 'categoryIds';
        }
    }

    public function getIncludeParentCategories(): ?bool
    {
        return false;
    }
}
