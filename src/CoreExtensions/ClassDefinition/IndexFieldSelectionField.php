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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CoreExtensions\ClassDefinition;

use Pimcore\Model\DataObject\ClassDefinition\Data\Textarea;

class IndexFieldSelectionField extends Textarea
{
    /**
     * @deprecated Will be removed in ecommerce-framework-bundle 2, use getFieldType() instead.
     */
    public string $fieldtype = 'indexFieldSelectionField';

    public bool $specificPriceField = false;

    public bool $showAllFields = false;

    public bool $considerTenants = false;

    public function setSpecificPriceField(bool $specificPriceField): void
    {
        $this->specificPriceField = $specificPriceField;
    }

    public function getSpecificPriceField(): bool
    {
        return $this->specificPriceField;
    }

    public function setShowAllFields(bool $showAllFields): void
    {
        $this->showAllFields = $showAllFields;
    }

    public function getShowAllFields(): bool
    {
        return $this->showAllFields;
    }

    public function setConsiderTenants(bool $considerTenants): void
    {
        $this->considerTenants = $considerTenants;
    }

    public function getConsiderTenants(): bool
    {
        return $this->considerTenants;
    }

    public function isEmpty(mixed $data): bool
    {
        if (is_string($data)) {
            return strlen($data) < 1;
        }
        if (is_array($data)) {
            return empty($data);
        }

        return true;
    }

    /**
     * @param null|\Pimcore\Model\DataObject\AbstractObject $object
     *
     */
    public function getDataFromEditmode(mixed $data, $object = null, array $params = []): string
    {
        if (is_array($data)) {
            $data = implode(',', $data);
        }

        return $data;
    }

    public function getFieldType(): string
    {
        return 'indexFieldSelectionField';
    }
}
