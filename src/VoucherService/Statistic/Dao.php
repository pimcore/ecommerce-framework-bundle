<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Statistic;

use Pimcore\Model\Exception\NotFoundException;

/**
 * @internal
 */
class Dao extends \Pimcore\Model\Dao\AbstractDao
{
    const TABLE_NAME = 'ecommerceframework_vouchertoolkit_statistics';

    public function __construct()
    {
        $this->db = \Pimcore\Db::get();
    }

    /**
     *
     *
     * @throws NotFoundException
     */
    public function getById(int $id): bool|string
    {
        $result = $this->db->fetchOne('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ? GROUP BY date', [$id]);
        if (empty($result)) {
            throw new NotFoundException('Statistic with id ' . $id . ' not found.');
        }
        $this->assignVariablesToModel($result);

        return $result;
    }
}
