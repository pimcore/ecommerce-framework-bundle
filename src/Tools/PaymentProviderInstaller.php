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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tools;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Objectbrick;

class PaymentProviderInstaller extends AbstractInstaller
{
    /**
     * @var string // json source path
     */
    protected string $bricksPath;

    /**
     * @var array<string, string> //$brickKey => $brickImportJsonPath
     */
    protected array $bricksToInstall = [];

    public function canBeInstalled(): bool
    {
        return !$this->isInstalled();
    }

    public function canBeUninstalled(): bool
    {
        return $this->isInstalled();
    }

    public function install(): void
    {
        $this->installBricks();
    }

    public function uninstall(): void
    {
        $this->unInstallBricks();
    }

    public function isInstalled(): bool
    {
        $installed = false;

        try {
            // check if payment brick exists
            foreach ($this->bricksToInstall as $brickKey => $brickFile) {
                $installed = Objectbrick\Definition::getByKey($brickKey);
            }
        } catch (\Exception $e) {
            // nothing to do
        }

        return (bool) $installed;
    }

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    protected function installBricks(): void
    {
        foreach ($this->bricksToInstall as $brickKey => $brickFile) {
            self::installBrick($brickKey, $this->bricksPath . $brickFile);
        }
    }

    protected function unInstallBricks(): void
    {
        foreach ($this->bricksToInstall as $brickKey => $brickFile) {
            $brick = Objectbrick\Definition::getByKey($brickKey);
            if ($brick instanceof Objectbrick\Definition) {
                $brick->delete();
            }
        }
    }

    protected static function installBrick(string $brickKey, string $filepath): void
    {
        try {
            $brick = Objectbrick\Definition::getByKey($brickKey);
        } catch (\Exception $e) {
            $brick = null;
        }

        if (!$brick) {
            $brick = new Objectbrick\Definition;
            $brick->setKey($brickKey);

            $json = file_get_contents($filepath);

            $success = Service::importObjectBrickFromJson($brick, $json);

            if ($success) {
                $onlineOrderClass = ClassDefinition::getByName('OnlineShopOrder');
                /** @var ClassDefinition\Data\Objectbricks $paymentProviderBrickField */
                $paymentProviderBrickField = $onlineOrderClass->getFieldDefinition('paymentProvider');
                $allowedTypes = $paymentProviderBrickField->getAllowedTypes();
                $paymentProviderBrickField->setAllowedTypes([$brickKey, ...$allowedTypes]);
            }
        }
    }
}
