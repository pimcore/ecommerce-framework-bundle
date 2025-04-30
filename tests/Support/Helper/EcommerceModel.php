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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tests\Support\Helper;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Tests\Support\Helper\Model;

class EcommerceModel extends Model
{
    protected function createClass(string $name, ClassDefinition\Layout $layout, string $filename, bool $inheritanceAllowed = false, ?string $id = null): ClassDefinition
    {
        //add index selection fields
        $rootPanel = $layout->getChildren();
        $mainPanel = $rootPanel[0]->getChildren()[0];

        $mainPanel->addChild($this->createDataChild('indexFieldSelection', 'indexFieldSelection', false));
        $mainPanel->addChild($this->createDataChild('indexFieldSelectionCombo', 'indexFieldSelectionCombo', false));
        $mainPanel->addChild($this->createDataChild('indexFieldSelectionField', 'indexFieldSelectionField', false));

        return parent::createClass($name, $layout, $filename, $inheritanceAllowed, $id);
    }

    public function createDataChild(string $type, ?string $name = null, bool $mandatory = false, bool $index = false, bool $visibleInGridView = true, bool $visibleInSearchResult = true): Data
    {
        if (!$name) {
            $name = $type;
        }

        if (strpos($type, 'indexField') === 0) {
            $classname = 'Pimcore\\Bundle\\EcommerceFrameworkBundle\\CoreExtensions\\ClassDefinition\\' . ucfirst($type);
        } else {
            $classname = 'Pimcore\\Model\\DataObject\\ClassDefinition\Data\\' . ucfirst($type);
        }
        /** @var Data $child */
        $child = new $classname();
        $child->setName($name);
        $child->setTitle($name);
        $child->setMandatory($mandatory);
        $child->setIndex($index);
        $child->setVisibleGridView($visibleInGridView);
        $child->setVisibleSearch($visibleInSearchResult);

        return $child;
    }
}
