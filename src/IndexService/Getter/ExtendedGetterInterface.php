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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Getter;

use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\ConfigInterface;

/**
 * Interface for getter of product index columns which consider sub object ids and tenant configs
 */
interface ExtendedGetterInterface extends GetterInterface
{
    public function get(object $object, ?array $config = null, ?int $subObjectId = null, ?ConfigInterface $tenantConfig = null): mixed;
}
