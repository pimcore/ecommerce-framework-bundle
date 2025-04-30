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

final class OrderAgentEvents
{
    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_INIT_PAYMENT = 'pimcore.ecommerce.orderagent.preInitPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_INIT_PAYMENT = 'pimcore.ecommerce.orderagent.postInitPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_START_PAYMENT = 'pimcore.ecommerce.orderagent.preStartPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_START_PAYMENT = 'pimcore.ecommerce.orderagent.postStartPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_CANCEL_PAYMENT = 'pimcore.ecommerce.orderagent.preCancelPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_CANCEL_PAYMENT = 'pimcore.ecommerce.orderagent.postCancelPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const FINGERPRINT_GENERATED = 'pimcore.ecommerce.orderagent.fingerPrintGenerated';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const PRE_UPDATE_PAYMENT = 'pimcore.ecommerce.orderagent.preUpdatePayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderAgentEvent")
     *
     * @var string
     */
    const POST_UPDATE_PAYMENT = 'pimcore.ecommerce.orderagent.postUpdatePayment';
}
