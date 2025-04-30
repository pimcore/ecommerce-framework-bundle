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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData;

class IndexFieldSelection
{
    public ?string $tenant = null;

    public string $field;

    /**
     * @var string|string[]|int|null
     */
    public string|array|int|null $preSelect;

    /**
     * @param string|string[]|int $preSelect
     */
    public function __construct(?string $tenant, string $field, array|string|int|null $preSelect)
    {
        $this->field = $field;
        $this->preSelect = $preSelect;
        $this->tenant = $tenant;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string|string[] $preSelect
     */
    public function setPreSelect(array|string|int $preSelect): void
    {
        $this->preSelect = $preSelect;
    }

    /**
     * @return string|string[]|null
     */
    public function getPreSelect(): array|string|int|null
    {
        return $this->preSelect;
    }

    public function setTenant(string $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function getTenant(): ?string
    {
        return $this->tenant;
    }
}
