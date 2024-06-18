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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\VoucherServiceException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;

interface VoucherServiceInterface
{
    /**
     * Gets the correct token manager and calls its checkToken() function.
     *
     *
     *
     * @throws VoucherServiceException
     */
    public function checkToken(string $code, CartInterface $cart): bool;

    /**
     * Gets the correct token manager and calls its reserveToken() function.
     *
     *
     */
    public function reserveToken(string $code, CartInterface $cart): bool;

    /**
     * Gets the correct token manager and calls its releaseToken() function, which removes a reservations.
     *
     *
     */
    public function releaseToken(string $code, CartInterface $cart): bool;

    /**
     * Gets the correct token manager and calls its applyToken() function, which returns
     * the ordered token object which gets appended to the order object. The token
     * reservations gets released.
     *
     *
     */
    public function applyToken(string $code, CartInterface $cart, AbstractOrder $order): bool;

    /**
     * Gets the correct token manager and calls removeAppliedTokenFromOrder(), which cleans up the
     * token usage and the ordered token object if necessary, removes the token object from the order.
     *
     *
     */
    public function removeAppliedTokenFromOrder(\Pimcore\Model\DataObject\OnlineShopVoucherToken $tokenObject, AbstractOrder $order): mixed;

    /**
     * Returns detail information of added voucher codes and if they are considered by pricing rules
     *
     *
     * @return PricingManagerTokenInformation[]
     */
    public function getPricingManagerTokenInformationDetails(CartInterface $cart, string $locale = null): array;

    /**
     * Cleans the token reservations due to sysConfig duration settings, if no series Id is
     * set all reservations older than the set duration get removed.
     */
    public function cleanUpReservations(int $seriesId = null): bool;

    /**
     * Removes all tokens from a voucher series and its reservations,
     * not considering any type of filter.
     *
     *
     */
    public function cleanUpVoucherSeries(\Pimcore\Model\DataObject\OnlineShopVoucherSeries $series): bool;

    /**
     * Removes all statistics, optionally a seriesId can be passed, to only remove from one series.
     */
    public function cleanUpStatistics(int $seriesId = null): bool;
}
