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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Command\IndexService;

use Exception;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\TenantConfigInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\ElasticSearch\AbstractElasticSearch;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\IndexRefreshInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\Worker\OpenSearch\AbstractOpenSearch;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class IndexSyncCommand extends AbstractIndexServiceCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('ecommerce:indexservice:search-index-sync')
            ->setDescription(
                'Refresh search index settings, mappings via native search API.'
            )
            ->addArgument('mode', InputArgument::REQUIRED,
                'reindex: Re-indexes search indices based on the their native reindexing API. Might be necessary when mapping has changed.'. PHP_EOL .
                'update-synonyms: Activate changes in synonym files, by closing and reopening the search index.'
            )
            ->addOption('tenant', null, InputOption::VALUE_OPTIONAL,
                'If a tenant name is provided (e.g. assortment_de), then only that specific tenant will be synced. '.
                'Otherwise all tenants will be synced.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mode = $input->getArgument('mode');
        $tenantName = $input->getOption('tenant');

        $indexService = Factory::getInstance()->getIndexService();
        $tenantList = $tenantName ? [$tenantName] : $indexService->getTenants();

        if (!in_array($mode, ['reindex', 'update-synonyms'])) {
            $output->writeln("<error>Unknown mode \"{$mode}\")...</error>");
            exit(1);
        }

        $bar = new ProgressBar($output, count($tenantList));

        foreach ($tenantList as $tenantName) {
            $elasticWorker = $indexService->getTenantWorker($tenantName); //e.g., 'AT_de_elastic'

            if (!$elasticWorker instanceof IndexRefreshInterface) {
                $output->writeln("<info>Skipping tenant \"{$tenantName}\" as it's not a valid search index tenant.</info>");

                continue;
            }

            $output->writeln("<info>Process tenant \"{$tenantName}\" (mode \"{$mode}\")...</info>");

            try {
                match ($mode) {
                    'reindex' => $elasticWorker->startReindexMode(),
                    'update-synonyms' => $elasticWorker->updateSynonyms(),
                    default => null,
                };
            } catch (Exception $e) {
                $output->writeln("<error>Failed to process tenant \"{$tenantName}\" (mode \"{$mode}\")...</error>");
                $output->writeln("<error>{$e->getMessage()}</error>");
            }

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
