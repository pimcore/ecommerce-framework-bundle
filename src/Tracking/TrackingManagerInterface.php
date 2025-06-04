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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking;

interface TrackingManagerInterface extends
    CategoryPageViewInterface,
    ProductImpressionInterface,
    ProductViewInterface,
    CartUpdateInterface,
    CartProductActionAddInterface,
    CartProductActionRemoveInterface,
    CheckoutInterface,
    CheckoutStepInterface,
    CheckoutCompleteInterface,
    TrackEventInterface
{
    /**
     * Returns the current javascript tracking codes for all trackers
     *
     */
    public function getTrackedCodes(): string;

    /**
     * Forwards all tracked tracking codes to the next request via FlashMesssageBag
     *
     * @return $this
     */
    public function forwardTrackedCodesAsFlashMessage(): static;
}
