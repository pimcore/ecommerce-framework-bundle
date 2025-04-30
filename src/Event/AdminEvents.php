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

final class AdminEvents
{
    /**
     * Fired when values in filter definition get fetched
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const GET_VALUES_FOR_FILTER_FIELD_PRE_SEND_DATA = 'pimcore.admin.ecommerce.getValuesForFilterFieldPreSendData';

    /**
     * Fired when filter fields get fetched
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const GET_INDEX_FIELD_NAMES_PRE_SEND_DATA = 'pimcore.admin.ecommerce.getIndexFieldNamesPreSendData';
}
