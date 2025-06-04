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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Command\Voucher;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class CleanupStatisticsCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('ecommerce:voucher:cleanup-statistics');
        $this->setDescription('House keeping for Voucher Usage Statistics - cleans up all old data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output->writeln('<comment>*</comment> Cleaning up <info>statistics</info>');
        Factory::getInstance()->getVoucherService()->cleanUpStatistics();

        return 0;
    }
}
