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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherTokenType;
use Pimcore\Model\DataObject\OnlineShopVoucherToken;

/**
 * Manager for specific token settings object of type AbstractVoucherTokenType.
 * Provides functionality for generating, checking, removing, applying, reserving tokens.
 * Also prepares the view of the Voucher Code Tab and specifies its template.
 */
interface TokenManagerInterface
{
    /**
     * Checks if the configuration objects contains valid values to
     * generate the new token(s).
     *
     */
    public function isValidSetting(): bool;

    /**
     * Removes tokens of series, if no parameters are passed, all tokens get removed from series.
     *
     * @param array|null $filter Associative with the indices: "usage" and "olderThan".
     *
     */
    public function cleanUpCodes(?array $filter = []): bool;

    /**
     * Checks a token code, if it is available for putting into cart
     * e.g. it is not reserved or used, or other tokenType specific settings.
     *
     *
     *
     * @throws \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\VoucherServiceException
     */
    public function checkToken(string $code, CartInterface $cart): bool;

    /**
     * Adds a reservation to a specific token code.
     *
     *
     *
     * @throws \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\VoucherServiceException
     */
    public function reserveToken(string $code, CartInterface $cart): bool;

    /**
     * Creates token object and adds it to order, increases token usage and
     * clears the reservation of the token.
     *
     *
     *
     * @throws \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\VoucherServiceException
     */
    public function applyToken(string $code, CartInterface $cart, AbstractOrder $order): OnlineShopVoucherToken|bool;

    /**
     * Removes the reservation of a token code.
     *
     *
     */
    public function releaseToken(string $code, CartInterface $cart): bool;

    /**
     * cleans up the token usage and the ordered token object if necessary
     *
     *
     */
    public function removeAppliedTokenFromOrder(OnlineShopVoucherToken $tokenObject, AbstractOrder $order): bool;

    /**
     * Get the codes of a voucher series, optionally a filter array can be passed.
     *
     *
     */
    public function getCodes(array $filter = null): bool|array;

    public function getStatistics(?int $usagePeriod = null): bool|array;

    public function insertOrUpdateVoucherSeries(): bool|string|array;

    public function getFinalTokenLength(): int;

    /**
     * Removes reservations
     *
     *
     */
    public function cleanUpReservations(int $duration = 0, ?int $seriesId = null): bool;

    /**
     * Prepares the view and returns the according template for rendering.
     * Gets the codes according to paging and filter params and sets
     * error/success messages, settings and statistics for view.
     *
     * @param array $params All params, especially for filtering and ordering token codes.
     *
     * @return string The path of the template to display.
     */
    public function prepareConfigurationView(array &$viewParamsBag, array $params): string;

    public function getConfiguration(): AbstractVoucherTokenType;
}
