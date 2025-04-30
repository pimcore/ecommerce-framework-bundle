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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\EventListener;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IndexUpdateListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::POST_ADD => 'onObjectUpdate',
            DataObjectEvents::POST_UPDATE => 'onObjectUpdate',
            DataObjectEvents::PRE_DELETE => 'onObjectDelete',
        ];
    }

    public function onObjectUpdate(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if ($object instanceof IndexableInterface && (!$event->hasArgument('saveVersionOnly') || !$event->getArgument('saveVersionOnly'))) {
            $indexService = Factory::getInstance()->getIndexService();
            $indexService->updateIndex($object);
        }
    }

    public function onObjectDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if ($object instanceof IndexableInterface) {
            $indexService = Factory::getInstance()->getIndexService();
            $indexService->deleteFromIndex($object);
        }

        // Delete tokens when a a configuration object gets removed.
        if ($object instanceof \Pimcore\Model\DataObject\OnlineShopVoucherSeries) {
            $voucherService = Factory::getInstance()->getVoucherService();
            $voucherService->cleanUpVoucherSeries($object);
        }
    }
}
