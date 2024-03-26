<?php

declare(strict_types=1);

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Migrations\AbstractMigration;

final class Version20240215093348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'remove o_ prefix from table names';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('ecommerceframework_productindex');

        foreach ($this->getPrefixedColumnNames() as $columnNamePrefixed) {
            if ($table->hasColumn($columnNamePrefixed)) {
                $columnNamePlain = ltrim($columnNamePrefixed, 'o_');
                $this->write(sprintf('Changing column [%s] to [%s] in table %s', $columnNamePrefixed, $columnNamePlain, $table->getName()));
                $this->renameColumn($columnNamePrefixed, $columnNamePlain, $table);
            } else {
                $this->write(sprintf('Column [%s] does not exist in table %s', $columnNamePrefixed, $table->getName()));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('ecommerceframework_productindex');

        foreach ($this->getPrefixedColumnNames() as $columnNamePrefixed) {
            $columnNamePlain = ltrim($columnNamePrefixed, 'o_');

            if ($table->hasColumn($columnNamePlain)) {
                $this->renameColumn($columnNamePlain, $columnNamePrefixed, $table);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getPrefixedColumnNames(): array
    {
        return [
            'o_id',
            'o_virtualProductId',
            'o_virtualProductActive',
            'o_classId',
            'o_parentId',
            'o_type',
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    private function renameColumn(string $from, string $to, Table $table): void
    {
        $this->addSql(
            sprintf(
                'ALTER TABLE ecommerceframework_productindex CHANGE %s %s %s %s;',
                $from,
                $to,
                $table->getColumn($from)->getType()->getSQLDeclaration(
                    $table->getColumn($from)->toArray(),
                    $this->platform
                ),
                $table->getColumn($from)->getNotnull() ? 'NOT NULL' : 'NULL',
            )
        );
    }
}
