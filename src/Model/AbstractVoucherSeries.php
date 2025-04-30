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

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager\TokenManagerInterface;

abstract class AbstractVoucherSeries extends \Pimcore\Model\DataObject\Concrete
{
    abstract public function getTokenSettings(): ?\Pimcore\Model\DataObject\Fieldcollection;

    public function getTokenManager(): ?TokenManagerInterface
    {
        $items = $this->getTokenSettings();

        if ($items && $items->get(0)) {
            // name of fieldcollection class
            /** @var AbstractVoucherTokenType $configuration */
            $configuration = $items->get(0);

            return Factory::getInstance()->getTokenManager($configuration);
        }

        return null;
    }

    public function getExistingLengths(): bool|array
    {
        $db = \Pimcore\Db::get();

        $query = '
            SELECT length, COUNT(*) AS count FROM ' . \Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Dao::TABLE_NAME . '
            WHERE voucherSeriesId = ?
            GROUP BY length';

        try {
            $lengths = $db->fetchAllAssociative($query, [$this->getId()]);

            $result = [];
            foreach ($lengths as $lengthEntry) {
                $result[$lengthEntry['length']] = $lengthEntry['count'];
            }

            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }
}
