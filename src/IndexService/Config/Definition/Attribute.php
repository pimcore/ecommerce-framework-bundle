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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\ConfigInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\ExtendedGetterInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter\InterpreterInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;

class Attribute
{
    private string $name;

    private string $fieldName;

    private ?string $type = null;

    private ?string $locale = null;

    private ?string $filterGroup = null;

    private array $options = [];

    private ?GetterInterface $getter = null;

    private array $getterOptions = [];

    private ?InterpreterInterface $interpreter = null;

    private array $interpreterOptions = [];

    private bool $hideInFieldlistDatatype = false;

    public function __construct(
        string $name,
        string $fieldName = null,
        string $type = null,
        string $locale = null,
        string $filterGroup = null,
        array $options = [],
        GetterInterface $getter = null,
        array $getterOptions = [],
        InterpreterInterface $interpreter = null,
        array $interpreterOptions = [],
        bool $hideInFieldlistDatatype = false
    ) {
        $this->name = $name;
        $this->fieldName = $fieldName ?? $name;
        $this->type = $type;
        $this->locale = $locale;
        $this->filterGroup = $filterGroup;
        $this->options = $options;

        $this->getter = $getter;
        $this->getterOptions = $getterOptions;

        $this->interpreter = $interpreter;
        $this->interpreterOptions = $interpreterOptions;

        $this->hideInFieldlistDatatype = $hideInFieldlistDatatype;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getFilterGroup(): ?string
    {
        return $this->filterGroup;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name, mixed $defaultValue = null): mixed
    {
        return $this->options[$name] ?? $defaultValue;
    }

    public function getGetter(): ?GetterInterface
    {
        return $this->getter;
    }

    public function getGetterOptions(): array
    {
        return $this->getterOptions;
    }

    public function getInterpreter(): ?InterpreterInterface
    {
        return $this->interpreter;
    }

    public function getInterpreterOptions(): array
    {
        return $this->interpreterOptions;
    }

    public function getHideInFieldlistDatatype(): bool
    {
        return $this->hideInFieldlistDatatype;
    }

    /**
     * Get value from object, running through getter if defined
     *
     *
     */
    public function getValue(IndexableInterface $object, int $subObjectId = null, ConfigInterface $tenantConfig = null, mixed $default = null): mixed
    {
        if (null !== $this->getter) {
            if ($this->getter instanceof ExtendedGetterInterface) {
                return $this->getter->get($object, $this->getterOptions, $subObjectId, $tenantConfig);
            } else {
                return $this->getter->get($object, $this->getterOptions);
            }
        }

        $getter = 'get' . ucfirst($this->fieldName);
        if (method_exists($object, $getter)) {
            return $object->$getter($this->locale);
        }

        return $default;
    }

    /**
     * Interpret value with interpreter if defined
     *
     *
     */
    public function interpretValue(mixed $value): mixed
    {
        if (null !== $this->interpreter) {
            return $this->interpreter->interpret($value, $this->interpreterOptions);
        }

        return $value;
    }
}
