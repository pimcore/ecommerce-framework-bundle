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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\Tracker\Analytics;

use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\Tracker as EcommerceTracker;

use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\TrackingItemBuilderInterface;
use Pimcore\Bundle\GoogleMarketingBundle\Tracker\TrackerInterface;
use Twig\Environment;

abstract class AbstractAnalyticsTracker extends EcommerceTracker
{
    protected TrackerInterface $tracker;

    /**
     * @internal
     *
     */
    public function __construct(
        TrackingItemBuilderInterface $trackingItemBuilder,
        Environment $twig,
        TrackerInterface $tracker,
        array $options = [],
        array $assortmentTenants = [],
        array $checkoutTenants = []
    ) {
        parent::__construct($trackingItemBuilder, $twig, $options, $assortmentTenants, $checkoutTenants);
        $this->tracker = $tracker;
    }
}
