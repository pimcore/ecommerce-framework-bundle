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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService;

use Pimcore\Db\Helper;
use Pimcore\Logger;
use Pimcore\Model\Exception\NotFoundException;

/**
 * @method Statistic\Dao getDao()
 */
class Statistic extends \Pimcore\Model\AbstractModel
{
    public int $id;

    public string $tokenSeriesId;

    public int $date;

    public function getById(int $id): Statistic|bool
    {
        try {
            $config = new self();
            $config->getDao()->getById($id);

            return $config;
        } catch (NotFoundException $ex) {
            //            Logger::debug($ex->getMessageN());
            return false;
        }
    }

    /**
     * @param int $seriesId
     * @param int|null $usagePeriod
     *
     * @return bool|array
     *
     * @throws \Exception
     */
    public static function getBySeriesId(int $seriesId, int $usagePeriod = null): bool|array
    {
        $db = \Pimcore\Db::get();

        $query = 'SELECT date, COUNT(*) as count FROM ' . \Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Statistic\Dao::TABLE_NAME . ' WHERE voucherSeriesId = ?';
        $params[] = $seriesId;
        if ($usagePeriod) {
            $query .= ' AND (TO_DAYS(NOW()) - TO_DAYS(date)) < ?';
            $params[] = $usagePeriod;
        }

        $query .= ' GROUP BY date';

        try {
            $result = Helper::fetchPairs($db, $query, $params);

            return $result;
        } catch (\Exception $e) {
            Logger::error('VoucherService', [$e]);

            return false;
        }
    }

    public static function increaseUsageStatistic(int $seriesId): bool
    {
        $db = $db = \Pimcore\Db::get();

        try {
            $db->executeQuery('INSERT INTO ' . \Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Statistic\Dao::TABLE_NAME . ' (voucherSeriesId,date) VALUES (?,NOW())', [(int)$seriesId]);

            return true;
        } catch (\Exception $e) {
            Logger::error('VoucherService', [$e]);

            return false;
        }
    }

    /**
     * @param int $duration days
     */
    public static function cleanUpStatistics(int $duration, int $seriesId = null): bool
    {
        $query = 'DELETE FROM ' . \Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Statistic\Dao::TABLE_NAME . ' WHERE DAY(DATEDIFF(date, NOW())) >= ?';
        $params[] = $duration;

        if (isset($seriesId)) {
            $query .= ' AND voucherSeriesId = ?';
            $params[] = $seriesId;
        }

        $db = \Pimcore\Db::get();

        try {
            $db->executeQuery($query, $params);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTokenSeriesId(): string
    {
        return $this->tokenSeriesId;
    }

    public function setTokenSeriesId(string $tokenSeriesId): void
    {
        $this->tokenSeriesId = $tokenSeriesId;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): void
    {
        $this->date = $date;
    }
}
