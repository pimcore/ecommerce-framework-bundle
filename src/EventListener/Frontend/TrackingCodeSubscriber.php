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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\EventListener\Frontend;

use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PreviewRequestTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\Tracker\GoogleTagManager;
use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\TrackingManager;
use Pimcore\Bundle\GoogleMarketingBundle\Event\GoogleTagManagerEvents;
use Pimcore\Bundle\GoogleMarketingBundle\Model\Event\TagManager\CodeEvent;
use Pimcore\Bundle\GoogleMarketingBundle\Tracker\Tracker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class TrackingCodeSubscriber implements EventSubscriberInterface
{
    use ResponseInjectionTrait;
    use PimcoreContextAwareTrait;
    use PreviewRequestTrait;

    protected TrackingManager $trackingManager;

    /** @var Environment * */
    protected Environment $twig;

    private bool $enabled = true;

    public function __construct(TrackingManager $trackingManager, Environment $twig)
    {
        $this->trackingManager = $trackingManager;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GoogleTagManagerEvents::CODE_HEAD => ['onCodeHead'],
        ];
    }

    public function onCodeHead(CodeEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $activeTrackers = $this->trackingManager->getActiveTrackers();

        foreach ($activeTrackers as $activeTracker) {
            if ($activeTracker instanceof GoogleTagManager) {
                $trackedCodes = $activeTracker->getTrackedCodes();

                if (empty($trackedCodes) || ! is_array($trackedCodes)) {
                    return;
                }

                $block = $event->getBlock(Tracker::BLOCK_BEFORE_SCRIPT_TAG);

                $code = $this->twig->render(
                    '@PimcoreGoogleMarketing/Analytics/Tracking/GoogleTagManager/dataLayer.html.twig',
                    ['trackedCodes' => $trackedCodes]
                );

                $block->prepend($code);
            }
        }
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
