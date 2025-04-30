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

final class CommitOrderProcessorEvents
{
    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_COMMIT_ORDER_PAYMENT = 'pimcore.ecommerce.commitorderprocessor.preCommitOrderPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const POST_COMMIT_ORDER_PAYMENT = 'pimcore.ecommerce.commitorderprocessor.postCommitOrderPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_COMMIT_ORDER = 'pimcore.ecommerce.commitorderprocessor.preCommitOrder';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const POST_COMMIT_ORDER = 'pimcore.ecommerce.commitorderprocessor.postCommitOrder';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_CLEANUP_PENDING_ORDER = 'pimcore.ecommerce.commitorderprocessor.preCleanupPendingOrder';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\CommitOrderProcessorEvent")
     *
     * @var string
     */
    const PRE_CLEANUP_PENDING_PAYMENT = 'pimcore.ecommerce.commitorderprocessor.preCleanupPendingPayment';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\SendConfirmationMailEvent")
     *
     * @var string
     */
    const SEND_CONFIRMATION_MAILS = 'pimcore.ecommerce.commitorderprocessor.sendConfirmationMails';
}
