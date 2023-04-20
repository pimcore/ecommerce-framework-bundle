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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\Helper;

use Doctrine\DBAL\Connection;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Config\MysqlConfigInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter\RelationInterpreterInterface;
use Pimcore\Cache;
use Pimcore\Db\Helper;
use Pimcore\Logger;

class MySql
{
    /**
     * @var string[]
     */
    protected array $_sqlChangeLog = [];

    protected MysqlConfigInterface $tenantConfig;

    protected Connection $db;

    public function __construct(MysqlConfigInterface $tenantConfig, Connection $db)
    {
        $this->tenantConfig = $tenantConfig;
        $this->db = $db;
    }

    /**
     * @return string[]
     */
    public function getPrimaryKey(string $table, bool $cache = true): array
    {
        return $this->getValidTableColumns($table, $cache, true);
    }

    /**
     * @return string[]
     */
    public function getValidTableColumns(string $table, bool $cache = true, bool $primaryKeyColumnsOnly = false): array
    {
        $cacheKey = 'plugin_ecommerce_productindex_columns_' . $table;

        if (!Cache\RuntimeCache::isRegistered($cacheKey) || !$cache) {
            $columns = [];
            $primaryKeyColumns = [];
            $data = $this->db->fetchAllAssociative('SHOW COLUMNS FROM ' . $table);
            foreach ($data as $d) {
                $fieldName = $d['Field'];
                $columns[] = $fieldName;
                if ($d['Key'] === 'PRI') {
                    $primaryKeyColumns[] = $fieldName;
                }
            }
            $allColumns = ['columns' => $columns,  'primaryKeyColumns' => $primaryKeyColumns];
            Cache\RuntimeCache::save($allColumns, $cacheKey);
        } else {
            $allColumns = Cache\RuntimeCache::load($cacheKey);
        }

        return $primaryKeyColumnsOnly ? $allColumns['primaryKeyColumns'] : $allColumns['columns'];
    }

    public function doInsertData(array $data): void
    {
        $validColumns = $this->getValidTableColumns($this->tenantConfig->getTablename());
        foreach ($data as $column => $value) {
            if (!in_array($column, $validColumns)) {
                unset($data[$column]);
            }
        }

        Helper::upsert($this->db, $this->tenantConfig->getTablename(), $data, $this->getPrimaryKey($this->tenantConfig->getTablename()));
    }

    /**
     * @return string[]
     */
    public function getSystemAttributes(): array
    {
        return ['id', 'classId', 'parentId', 'virtualProductId', 'virtualProductActive', 'type', 'categoryIds', 'parentCategoryIds', 'priceSystemName', 'active', 'inProductList'];
    }

    public function createOrUpdateIndexStructures(): void
    {
        $primaryIdColumnType = $this->tenantConfig->getIdColumnType(true);
        $idColumnType = $this->tenantConfig->getIdColumnType(false);

        $this->dbexec('CREATE TABLE IF NOT EXISTS `' . $this->tenantConfig->getTablename() . "` (
          `id` $primaryIdColumnType,
          `virtualProductId` $idColumnType,
          `virtualProductActive` TINYINT(1) NOT NULL,
          `classId` varchar(50) NOT NULL,
          `parentId` $idColumnType,
          `type` varchar(20) NOT NULL,
          `categoryIds` varchar(255) NOT NULL,
          `parentCategoryIds` varchar(255) NOT NULL,
          `priceSystemName` varchar(50) NOT NULL,
          `active` TINYINT(1) NOT NULL,
          `inProductList` TINYINT(1) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $data = $this->db->fetchAllAssociative('SHOW COLUMNS FROM ' . $this->tenantConfig->getTablename());
        $columns = [];
        foreach ($data as $d) {
            $columns[$d['Field']] = $d;
        }

        $systemColumns = $this->getSystemAttributes();

        $columnsToDelete = $columns;
        $columnsToAdd = [];
        $columnsToModify = [];

        foreach ($this->tenantConfig->getAttributes() as $attribute) {
            if (!array_key_exists($attribute->getName(), $columns)) {
                $doAdd = true;
                if (null !== $attribute->getInterpreter() && $attribute->getInterpreter() instanceof  RelationInterpreterInterface) {
                    $doAdd = false;
                }

                if ($doAdd) {
                    $columnsToAdd[$attribute->getName()] = $attribute->getType();
                }
            } elseif ($attribute->getType() != $columns[$attribute->getName()]['Type']) {
                $columnsToModify[$attribute->getName()] = $attribute->getType();
            }

            unset($columnsToDelete[$attribute->getName()]);
        }

        foreach ($columnsToDelete as $c) {
            if (!in_array($c['Field'], $systemColumns)) {
                $this->dbexec('ALTER TABLE `' . $this->tenantConfig->getTablename() . '` DROP COLUMN `' . $c['Field'] . '`;');
            }
        }

        foreach ($columnsToAdd as $c => $type) {
            $this->dbexec('ALTER TABLE `' . $this->tenantConfig->getTablename() . '` ADD `' . $c . '` ' . $type . ';');
        }

        foreach ($columnsToModify as $c => $type) {
            $this->dbexec('ALTER TABLE `' . $this->tenantConfig->getTablename() . '` MODIFY `' . $c . '` ' . $type . ';');
        }

        $searchIndexColumns = $this->tenantConfig->getSearchAttributes();
        if (!empty($searchIndexColumns)) {
            try {
                $this->dbexec('ALTER TABLE ' . $this->tenantConfig->getTablename() . ' DROP INDEX search;');
            } catch (\Exception $e) {
                Logger::info((string) $e);
            }

            $this->dbexec('ALTER TABLE `' . $this->tenantConfig->getTablename() . '` ENGINE = InnoDB;');
            $columnNames = [];
            foreach ($searchIndexColumns as $c) {
                $columnNames[] = $this->db->quoteIdentifier($c);
            }
            $this->dbexec('ALTER TABLE `' . $this->tenantConfig->getTablename() . '` ADD FULLTEXT INDEX search (' . implode(',', $columnNames) . ');');
        }

        $this->dbexec('CREATE TABLE IF NOT EXISTS `' . $this->tenantConfig->getRelationTablename() . "` (
          `src` $idColumnType,
          `src_virtualProductId` int(11) NOT NULL,
          `dest` int(11) NOT NULL,
          `fieldname` varchar(255) COLLATE utf8_bin NOT NULL,
          `type` varchar(20) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`src`,`dest`,`fieldname`,`type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

        if ($this->tenantConfig->getTenantRelationTablename()) {
            $this->dbexec('CREATE TABLE IF NOT EXISTS `' . $this->tenantConfig->getTenantRelationTablename() . "` (
              `id` $idColumnType,
              `subtenant_id` int(11) NOT NULL,
              PRIMARY KEY (`id`,`subtenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
        }
    }

    protected function dbexec(string $sql): void
    {
        $this->logSql($sql);
        $this->db->executeQuery($sql);
    }

    protected function logSql(string $sql): void
    {
        Logger::info($sql);

        $this->_sqlChangeLog[] = $sql;
    }

    public function __destruct()
    {
        // write sql change log for deploying to production system
        if (!empty($this->_sqlChangeLog)) {
            $log = implode("\n\n\n", $this->_sqlChangeLog);

            $filename = 'db-change-log_'.time().'_productindex.sql';
            $file = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/'.$filename;
            if (defined('PIMCORE_DB_CHANGELOG_DIRECTORY')) {
                $file = PIMCORE_DB_CHANGELOG_DIRECTORY.'/'.$filename;
            }

            file_put_contents($file, $log);
        }
    }
}
