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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\OrderManager;

/**
 * Class AbstractListItem
 * template method pattern
 */
abstract class AbstractOrderListItem
{
    protected array $resultRow;

    public function __construct(array $resultRow)
    {
        $this->resultRow = $resultRow;
    }

    abstract public function getId(): int;
}
