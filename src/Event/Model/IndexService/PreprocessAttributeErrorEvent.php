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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\IndexService;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition\Attribute;

class PreprocessAttributeErrorEvent extends PreprocessErrorEvent
{
    protected Attribute $attribute;

    protected bool $skipAttribute = false;

    /**
     * PreprocessAttributeErrorEvent constructor.
     *
     */
    public function __construct(Attribute $attribute, \Throwable $exception, bool $skipAttribute = false, bool $throwException = true)
    {
        parent::__construct($exception, $throwException);
        $this->attribute = $attribute;
        $this->skipAttribute = $skipAttribute;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function doSkipAttribute(): bool
    {
        return $this->skipAttribute;
    }

    public function setSkipAttribute(bool $skipAttribute): PreprocessErrorEvent
    {
        $this->skipAttribute = $skipAttribute;

        return $this;
    }
}
