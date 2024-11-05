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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config;

/**
 * @deprecated This interface will be removed in version 2.0.0 Use SearchConfigInterface instead.
 */
<<<<<<<< HEAD:src/IndexService/Config/SearchConfigInterface.php
interface SearchConfigInterface extends ConfigInterface
========
interface ElasticSearchConfigInterface extends SearchConfigInterface
>>>>>>>> origin/2.x:src/IndexService/Config/ElasticSearchConfigInterface.php
{
}
