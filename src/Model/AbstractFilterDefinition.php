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

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\Fieldcollection;

/**
 * Abstract base class for filter definition pimcore objects
 */
abstract class AbstractFilterDefinition extends DataObject\Concrete implements DataObject\PreGetValueHookInterface
{
    /**
     * returns page limit for product list
     *
     * @todo Convert return typehint to ?int in the next major update.
     */
    abstract public function getPageLimit(): float|int|null;

    /**
     * returns list of available fields for sorting ascending
     *
     *
     */
    abstract public function getOrderByAsc(): ?string;

    /**
     * returns list of available fields for sorting descending
     *
     *
     */
    abstract public function getOrderByDesc(): ?string;

    /**
     * return array of field collections for preconditions
     *
     *
     * @return Fieldcollection<AbstractFilterDefinitionType>|null
     */
    abstract public function getConditions(): ?Fieldcollection;

    /**
     * return array of field collections for filters
     *
     *
     * @return Fieldcollection<AbstractFilterDefinitionType>|null
     */
    abstract public function getFilters(): ?Fieldcollection;

    /**
     * enables inheritance for field collections, if xxxInheritance field is available and set to string 'true'
     *
     *
     */
    public function preGetValue(string $key): ?Fieldcollection
    {
        if ($this->getClass()->getAllowInherit()
            && DataObject::doGetInheritedValues()
            && $this->getClass()->getFieldDefinition($key) instanceof DataObject\ClassDefinition\Data\Fieldcollections
        ) {
            $checkInheritanceKey = $key . 'Inheritance';
            if ($this->{
                'get' . $checkInheritanceKey
                }() == 'true'
            ) {
                try {
                    $parentValue = $this->getValueFromParent($key);
                } catch (InheritanceParentNotFoundException $e) {
                    $parentValue = null;
                }

                $data = $this->$key;
                if (!$data) {
                    $data = $this->getClass()->getFieldDefinition($key)->preGetData($this);
                }
                if (!$data) {
                    return $parentValue;
                }
                if (!empty($parentValue)) {
                    $value = new Fieldcollection($parentValue->getItems());
                    foreach ($data as $entry) {
                        $value->add($entry);
                    }
                } else {
                    $value = new Fieldcollection($data->getItems());
                }

                return $value;
            }
        }

        return null;
    }
}
