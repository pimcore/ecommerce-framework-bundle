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

final class OrderManagerEvents
{
    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerEvent")
     *
     * @var string
     */
    const PRE_GET_OR_CREATE_ORDER_FROM_CART = 'pimcore.ecommerce.ordermanager.preGetOrCreateOrderFromCart';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerEvent")
     *
     * @var string
     */
    const PRE_UPDATE_ORDER = 'pimcore.ecommerce.ordermanager.preUpdateOrder';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerEvent")
     *
     * @var string
     */
    const POST_UPDATE_ORDER = 'pimcore.ecommerce.ordermanager.postUpdateOrder';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerItemEvent")
     *
     * @var string
     */
    const POST_CREATE_ORDER_ITEM = 'pimcore.ecommerce.ordermanager.postCreateOrderItem';

    /**
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\OrderManagerItemEvent")
     *
     * @var string
     */
    const BUILD_ORDER_ITEM_KEY = 'pimcore.ecommerce.ordermanager.buildOrderItemKey';
}
