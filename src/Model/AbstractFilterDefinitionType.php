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

use Pimcore\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection;

/**
 * Abstract base class for filter definition type field collections
 */
abstract class AbstractFilterDefinitionType extends \Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData
{
    protected array $metaData = [];

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function setMetaData(array $metaData): static
    {
        $this->metaData = $metaData;

        return $this;
    }

    abstract public function getLabel(): ?string;

    abstract public function getField(): string|IndexFieldSelection|null;

    abstract public function getScriptPath(): ?string;

    public function getRequiredFilterField(): string
    {
        return '';
    }
}
