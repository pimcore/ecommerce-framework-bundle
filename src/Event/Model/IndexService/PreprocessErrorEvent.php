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

use Symfony\Contracts\EventDispatcher\Event;

class PreprocessErrorEvent extends Event
{
    protected \Throwable $exception;

    protected bool $throwException;

    protected int $subObjectId;

    /**
     * PreprocessErrorEvent constructor.
     *
     */
    public function __construct(\Throwable $exception, bool $throwException = true, int $subObjectId = 0)
    {
        $this->exception = $exception;
        $this->throwException = $throwException;
        $this->subObjectId = $subObjectId;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function setThrowException(bool $throwException): void
    {
        $this->throwException = $throwException;
    }

    public function doThrowException(): bool
    {
        return $this->throwException;
    }

    public function getSubObjectId(): int
    {
        return $this->subObjectId;
    }

    public function setSubObjectId(int $subObjectId): void
    {
        $this->subObjectId = $subObjectId;
    }
}
