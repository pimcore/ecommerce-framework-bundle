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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Event;

final class CheckoutManagerEvents
{
    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CheckoutManagerStepsEvent")
     *
     * @var string
     */
    const PRE_COMMIT_STEP = 'pimcore.ecommerce.checkoutmanager.preCommitStep';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CheckoutManagerStepsEvent")
     *
     * @var string
     */
    const POST_COMMIT_STEP = 'pimcore.ecommerce.checkoutmanager.postCommitStep';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CheckoutManagerStepsEvent")
     *
     * @var string
     */
    const INITIALIZE_STEP_STATE = 'pimcore.ecommerce.checkoutmanager.initializeStepState';
}
