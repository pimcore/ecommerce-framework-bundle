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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use Pimcore\Model\DataObject\Concrete;

abstract class AbstractObjectListCondition
{
    /**
     * Handle serializing of object list to list of IDs
     *
     *
     */
    protected function handleSleep(string $objectProperty, string $idProperty): array
    {
        $itemIds = [];

        /** @var Concrete $item */
        foreach ($this->$objectProperty as $key => $item) {
            $itemIds[$key] = $item->getId();
        }

        $this->$idProperty = $itemIds;

        return [$idProperty];
    }

    /**
     * Handle loading of object list from serialized list of IDs
     *
     */
    protected function handleWakeup(string $objectProperty, string $idProperty): void
    {
        // support for legacy version with IDs serialized directly to property
        $source = (null !== $this->$objectProperty && count($this->$objectProperty) > 0)
            ? $this->$objectProperty
            : $this->$idProperty;

        $items = [];
        if (null !== $source && is_array($source)) {
            foreach ($source as $key => $sourceId) {
                $item = $this->loadObject($sourceId);

                if ($item) {
                    $items[$key] = $item;
                }
            }
        }

        $this->$objectProperty = $items;
    }

    /**
     * Load object by ID
     *
     *
     */
    protected function loadObject(int $id): ?Concrete
    {
        return Concrete::getById($id);
    }
}
