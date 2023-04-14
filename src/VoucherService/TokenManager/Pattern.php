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

use Knp\Component\Pager\PaginatorInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\VoucherServiceException;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherTokenType;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Reservation;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Statistic;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Token;
use Pimcore\Bundle\EcommerceFrameworkBundle\VoucherService\Token\Listing;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\DataObject\Fieldcollection\Data\VoucherTokenTypePattern;
use Pimcore\Model\DataObject\OnlineShopVoucherSeries;
use Pimcore\Model\DataObject\OnlineShopVoucherToken;

/**
 * @property \Pimcore\Model\DataObject\Fieldcollection\Data\VoucherTokenTypePattern $configuration
 */
class Pattern extends AbstractTokenManager implements ExportableTokenManagerInterface
{
    /**
     * Max probability to hit a duplicate entry on insertion e.g. to guess a code
     */
    const MAX_PROBABILITY = 0.005;

    protected string $template;

    /**
     * @var array<string, string>
     */
    protected array $characterPools = [
        'alphaNumeric' => '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
        'numeric' => '123456789',
        'alpha' => 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
    ];

    public function __construct(AbstractVoucherTokenType $configuration, protected PaginatorInterface $paginator)
    {
        parent::__construct($configuration);
        if ($configuration instanceof VoucherTokenTypePattern) {
            $this->template = '@PimcoreEcommerceFramework/voucher/voucher_code_tab_pattern.html.twig';
        } else {
            throw new InvalidConfigException('Invalid Configuration Class for Type VoucherTokenTypePattern.');
        }
    }

    public function isValidSetting(): bool
    {
        if ($this->characterPoolExists($this->configuration->getCharacterType()) && $this->configuration->getLength() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param array|null $filter Associative with the indices: "usage" and "olderThan".
     *
     * @return bool
     */
    public function cleanUpCodes(?array $filter = []): bool
    {
        return Listing::cleanUpTokens($this->seriesId, $filter);
    }

    /**
     * @param string $code
     * @param CartInterface $cart
     *
     * @return bool
     *
     * @throws VoucherServiceException
     */
    public function checkToken(string $code, CartInterface $cart): bool
    {
        parent::checkToken($code, $cart);
        if ($token = Token::getByCode($code)) {
            if ($token->isUsed()) {
                throw new VoucherServiceException('Token has already been used.', VoucherServiceException::ERROR_CODE_TOKEN_ALREADY_IN_USE);
            }
            if ($token->isReserved()) {
                throw new VoucherServiceException('Token has already been reserved.', VoucherServiceException::ERROR_CODE_TOKEN_ALREADY_RESERVED);
            }
        }

        return true;
    }

    /**
     * @param string $code
     * @param CartInterface $cart
     *
     * @return bool
     *
     * @throws VoucherServiceException
     */
    public function reserveToken(string $code, CartInterface $cart): bool
    {
        if (Token::getByCode($code)) {
            if (Reservation::create($code, $cart)) {
                return true;
            }

            throw new VoucherServiceException('Token Reservation not possible.', VoucherServiceException::ERROR_CODE_TOKEN_RESERVATION_NOT_POSSIBLE);
        }

        throw new VoucherServiceException('No Token for this code exists.', VoucherServiceException::ERROR_CODE_NO_TOKEN_FOR_THIS_CODE_EXISTS);
    }

    /**
     * @param string $code
     * @param CartInterface $cart
     * @param AbstractOrder $order
     *
     * @return bool|OnlineShopVoucherToken
     *
     * @throws VoucherServiceException
     *
     */
    public function applyToken(string $code, CartInterface $cart, AbstractOrder $order): OnlineShopVoucherToken|bool
    {
        if ($token = Token::getByCode($code)) {
            if ($token->isUsed()) {
                throw new VoucherServiceException('Token has already been used.', VoucherServiceException::ERROR_CODE_TOKEN_ALREADY_IN_USE);
            }
            if ($token->apply()) {
                $orderToken = new OnlineShopVoucherToken();
                $orderToken->setTokenId($token->getId());
                $orderToken->setToken($token->getToken());
                $series = OnlineShopVoucherSeries::getById($token->getVoucherSeriesId());
                $orderToken->setVoucherSeries($series);
                $orderToken->setParent($series);
                $orderToken->setKey(File::getValidFilename($token->getToken()));
                $orderToken->setPublished(true);
                $orderToken->save();

                return $orderToken;
            }
        }

        return false;
    }

    /**
     * cleans up the token usage and the ordered token object if necessary
     *
     * @param OnlineShopVoucherToken $tokenObject
     * @param AbstractOrder $order
     *
     * @return bool
     */
    public function removeAppliedTokenFromOrder(OnlineShopVoucherToken $tokenObject, AbstractOrder $order): bool
    {
        if ($token = Token::getByCode($tokenObject->getToken())) {
            $token->unuse();
            $tokenObject->delete();

            return true;
        } else {
            return false;
        }
    }

    public function releaseToken(string $code, CartInterface $cart): bool
    {
        return Reservation::releaseToken($code);
    }

    /**
     * @param array|null $filter
     *
     * @return array|bool
     */
    public function getCodes(array $filter = null): bool|array
    {
        return Token\Listing::getCodes($this->seriesId, $filter);
    }

    public function getStatistics(int $usagePeriod = null): array
    {
        $overallCount = Token\Listing::getCountBySeriesId($this->seriesId);
        $usageCount = Token\Listing::getCountByUsages(1, $this->seriesId);
        $reservedTokenCount = Token\Listing::getCountByReservation($this->seriesId);

        $usage = Statistic::getBySeriesId($this->seriesId, $usagePeriod);
        if (is_array($usage)) {
            $this->prepareUsageStatisticData($usage, $usagePeriod);
        }

        return [
            'overallCount' => $overallCount,
            'usageCount' => $usageCount,
            'freeCount' => $overallCount - $usageCount - $reservedTokenCount,
            'reservedCount' => $reservedTokenCount,
            'usage' => $usage,
        ];
    }

    /**
     * Generates Codes and an according Insert Query, if the MAX_PACKAGE_SIZE
     * may be reached several queries are generated.
     * returns the generated voucher codes if it was successfully - otherwise false
     *
     * @return bool | string | array  - bool failed - array if codes are generated
     */
    public function insertOrUpdateVoucherSeries(): bool|string|array
    {
        $db = \Pimcore\Db::get();

        try {
            $codeSets = $this->generateCodes();

            if ($codeSets === false) {
                return false;
            }

            if (is_array($codeSets)) {
                foreach ($codeSets as $query) {
                    $db->executeQuery($this->buildInsertQuery($query));
                }
            }

            return $codeSets;
        } catch (\Exception $e) {
            Logger::error((string) $e);
        }

        return false;
    }

    /**
     * Gets the final length of the token, incl.
     * prefix and separators.
     *
     * @return  int
     */
    public function getFinalTokenLength(): int
    {
        $separatorCount = $this->configuration->getSeparatorCount();
        $separator = $this->configuration->getSeparator();
        $prefix = $this->configuration->getPrefix();
        if (!empty($separator)) {
            if (!empty($prefix)) {
                return strlen($this->configuration->getPrefix()) + 1 + (int) floor($this->configuration->getLength() / $separatorCount) + $this->configuration->getLength();
            }

            return (int) floor($this->configuration->getLength() / $separatorCount) + $this->configuration->getLength();
        }

        return strlen($this->configuration->getPrefix()) + $this->configuration->getLength();
    }

    /**
     * Calculates the probability to hit an existing value on a token generation.
     *
     * @return float
     */
    public function getInsertProbability(): float
    {
        $maxCount = $this->getMaxCount();

        $dbCount = Token\Listing::getCountByLength($this->getFinalTokenLength(), $this->seriesId);

        if ($dbCount !== null && $maxCount >= 0) {
            return ((int)$dbCount + $this->configuration->getCount()) / $maxCount;
        }

        return 1.0;
    }

    protected function isValidGeneration(): bool
    {
        if (!$this->isValidSetting()) {
            return false;
        }
        $insertProbability = $this->getInsertProbability();
        if ($insertProbability <= self::MAX_PROBABILITY) {
            return true;
        }

        return false;
    }

    /**
     * Calculates the max possible amount of tokens for the specified character pool.
     */
    protected function getMaxCount(): int|float
    {
        $count = strlen($this->getCharacterPool());

        return pow($count, $this->configuration->getLength());
    }

    /**
     * Generates a single code.
     *
     * @return string
     */
    protected function generateCode(): string
    {
        $key = '';
        $charPool = $this->getCharacterPool();
        $size = strlen($charPool);
        for ($i = 0; $i < $this->configuration->getLength(); $i++) {
            $rand = mt_rand(0, $size - 1);
            $key .= $charPool[$rand];
        }

        return $key;
    }

    /**
     * Puts the code in the defined format. Incl. prefix and separators.
     *
     * @param string $code Generated Code.
     *
     * @return string formatted Code.
     */
    protected function formatCode(string $code): string
    {
        $separator = $this->configuration->getSeparator();
        $prefix = $this->getConfiguration()->getPrefix();
        if (!empty($separator)) {
            if (!empty($prefix)) {
                $code = $this->configuration->getPrefix() . $separator . implode($separator, str_split($code, $this->configuration->getSeparatorCount()));
            } else {
                $code = implode($separator, str_split($code, $this->configuration->getSeparatorCount()));
            }
        } else {
            $code = $this->configuration->getPrefix() . $code;
        }

        return $code;
    }

    /**
     * Checks whether a token is in the an array of tokens, the token is the key of the array.
     *
     * @param array|string $tokens One or more tokens.
     * @param array $cTokens Array of tokens.
     *
     * @return bool
     */
    protected function tokenExists(array|string $tokens, array $cTokens): bool
    {
        if (!is_array($tokens)) {
            $tokens = [$tokens];
        }
        $check = array_intersect_key($tokens, $cTokens);

        if (!empty($check)) {
            return true;
        }

        return false;
    }

    /**
     * Builds an insert query for an array of tokens.
     *
     * @param array $insertTokens
     *
     * @return string
     */
    protected function buildInsertQuery(array $insertTokens): string
    {
        $finalLength = $this->getFinalTokenLength();
        $insertParts = [];

        foreach ($insertTokens as $token) {
            $insertParts[] =
                "('" .
                $token .
                "'," .
                $finalLength .
                ',' .
                $this->seriesId .
                ')';
        }

        return 'INSERT INTO ' . Token\Dao::TABLE_NAME . '(token,length,voucherSeriesId) VALUES ' . implode(',', $insertParts);
    }

    /**
     * Generates a set of unique tokens according to the given token settings.
     * Returns false if the generation is not possible, due to set insert
     * probability MAX_INSERT_PROBABILITY.
     *
     * @return array|bool
     */
    public function generateCodes(): bool|array
    {
        // Size of one segment of tokens to check against the db.
        $tokenCheckStep = ceil($this->configuration->getCount() / 250);

        if ($this->isValidGeneration()) {
            $finalTokenLength = $this->getFinalTokenLength();
            // Check if a max_packet_size Error is possible
            $possibleMaxQuerySizeError = ($finalTokenLength * $this->configuration->getCount() / 1024 / 1024) > 15;
            // Return Query
            $resultTokenSet = [];
            // Tokens of one Insert Query
            $insertTokens = [];
            // Tokens of all Insert Queries together
            $insertCheckTokens = [];
            // Tokens of one segment of tokens to check against if they already exist in the db
            $checkTokens = [];

            // Count for all tokens to insert into db
            $insertCount = 0;
            // Count for tokens to check in db in on segment
            $checkTokenCount = 0;

            // Create unique tokens
            while ($insertCount < $this->configuration->getCount()) {
                // Considerations for last Couple of tokens, so that the amount of overall tokens is correct.
                if ($this->configuration->getCount() > ($insertCount + $checkTokenCount)) {
                    $token = $this->formatCode($this->generateCode());
                    // If the key already exists in the current checkTokens Segment,
                    // do not increase the checkTokensCount
                    if (!array_key_exists($token, $checkTokens)) {
                        $checkTokens[$token] = $token;
                        $checkTokenCount++;
                    }
                } else {
                    $token = null;
                    $checkTokenCount++;
                }

                // Check the temp array checkTokens if the just generated token already exists.
                // If so, unset the last token and decrease the count for the array of tokens to check
                if ($this->tokenExists($checkTokens, $insertCheckTokens)) {
                    $checkTokenCount--;
                    unset($checkTokens[$token]);
                // Check if the length of the checkTokens Array matches the defined step range
                // so the the checkTokens get matched against the database.
                } elseif ($checkTokenCount == $tokenCheckStep) {
                    // Check if any of the tokens in the temporary array checkTokens already exists,
                    // if not so, merge the checkTokens array with the array of tokens to insert and
                    // increase the overall count by the length of the checkArray i.e. the checkTokenStep
                    if (!Token\Listing::tokensExist($checkTokens)) {
                        $insertTokens = array_merge($insertTokens, $checkTokens);
                        $insertCount += $tokenCheckStep;
                    }
                    $checkTokenCount = 0;
                    $checkTokens = [];

                    // If an max_package_size error is possible build a new insert query.
                    if ($possibleMaxQuerySizeError) {
                        if (($insertCount * $finalTokenLength / 1024 / 1024) > 15) {
                            $resultTokenSet[] = $insertTokens;
                            $insertCheckTokens = array_merge($insertTokens, $insertCheckTokens);
                            $insertTokens = [];
                        }
                    } else {
                        // If no Error is possible or insert query needed, the overall tokens
                        // are the insert tokens of the current query, because there will be only
                        // one or no query.
                        $insertCheckTokens = $insertTokens;
                    }
                }
            }

            // If there are tokens to insert add them to query.
            if (count($insertTokens)) {
                $resultTokenSet[] = $insertTokens;
            }

            return $resultTokenSet;
        }

        return false;
    }

    /**
     * Creates an array with the indices of days of the given usage period.
     *
     * @param array $data
     * @param int $usagePeriod
     */
    protected function prepareUsageStatisticData(array &$data, int $usagePeriod): void
    {
        $now = new \DateTime();
        $periodData = [];
        for ($i = $usagePeriod; $i > 0; $i--) {
            $index = $now->format('Y-m-d');
            $periodData[$index] = isset($data[$index]) ? $data[$index] : 0;
            $now->modify('-1 day');
        }
        $data = $periodData;
    }

    /**
     * Prepares the view and returns the according template for rendering.
     *
     * @param array $viewParamsBag
     * @param array $params
     *
     * @return string
     */
    public function prepareConfigurationView(array &$viewParamsBag, array $params): string
    {
        $viewParamsBag['msg'] = [];

        $tokens = new Token\Listing();

        try {
            $tokens->setFilterConditions((int) $params['id'], $params);
        } catch (\Exception $e) {
            $this->template = '@PimcoreEcommerceFramework/voucher/voucher_code_tab_error.html.twig';
            $viewParamsBag['errors'][] = $e->getMessage() . ' | Error-Code: ' . $e->getCode();
        }

        $page = (int)($params['page'] ?? 1);
        $perPage = (int)($params['tokensPerPage'] ?? 25);

        $total = count($tokens);

        $availablePages = (int) ceil($total / $perPage);
        $page = min($page, $availablePages);

        $paginator = $this->paginator->paginate(
            $tokens,
            $page ?: 1,
            $perPage
        );

        $viewParamsBag['paginator'] = $paginator;
        $viewParamsBag['count'] = $total;

        $viewParamsBag['msg']['error'] = $params['error'] ?? null;
        $viewParamsBag['msg']['success'] = $params['success'] ?? null;

        // Settings parsed via foreach in view -> key is translation
        $viewParamsBag['settings'] = [
            'bundle_ecommerce_voucherservice_settings-count' => $this->getConfiguration()->getCount(),
            'bundle_ecommerce_voucherservice_settings-prefix' => $this->getConfiguration()->getPrefix(),
            'bundle_ecommerce_voucherservice_settings-length' => $this->getConfiguration()->getLength(),
            'bundle_ecommerce_voucherservice_settings-exampletoken' => $this->getExampleToken(),
        ];

        $statisticUsagePeriod = 30;

        if (isset($params['statisticUsagePeriod'])) {
            $statisticUsagePeriod = $params['statisticUsagePeriod'];
        }

        $viewParamsBag['tokenLengths'] = $this->series->getExistingLengths();

        $viewParamsBag['statistics'] = $this->getStatistics($statisticUsagePeriod);

        return $this->template;
    }

    /**
     * Get data for export
     *
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getExportData(array $params): array
    {
        $tokens = new Token\Listing();
        $tokens->setFilterConditions((int) $params['id'], $params);

        $data = [];

        foreach ($tokens as $token) {
            $data[] = [
                'token' => $token->getToken(),
                'usages' => $token->getUsages(),
                'length' => $token->getLength(),
                'timestamp' => $token->getTimestamp(),
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUpReservations(int $duration = 0, ?int $seriesId = null): bool
    {
        return Reservation::cleanUpReservations($duration, $this->seriesId);
    }

    /**
     * Checks whether an index for the given name parameter exists in
     * the character pool member array.
     *
     * @param string $poolName
     *
     * @return bool
     */
    protected function characterPoolExists(string $poolName): bool
    {
        return array_key_exists($poolName, $this->getCharacterPools());
    }

    /**
     * Generates and returns an example token to the given settings.
     *
     * @return string
     */
    public function getExampleToken(): string
    {
        return $this->formatCode($this->generateCode());
    }

    /**
     * Getters and Setters
     */
    public function getConfiguration(): VoucherTokenTypePattern
    {
        return $this->configuration;
    }

    public function setConfiguration(VoucherTokenTypePattern $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array<string, string>
     */
    public function getCharacterPools(): array
    {
        return $this->characterPools;
    }

    public function getCharacterPool(): string
    {
        return $this->characterPools[$this->configuration->getCharacterType()];
    }

    /**
     * @param array<string, string> $characterPools
     */
    public function setCharacterPools(array $characterPools): void
    {
        $this->characterPools = $characterPools;
    }

    /**
     * @param array<string, string> $pool Associative Array - the key represents the name, the value the characters of the character-pool. i.e.:"['numeric'=>'12345']"
     */
    public function addCharacterPool(array $pool): void
    {
        $this->characterPools = array_merge($this->characterPools, $pool);
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function setSeriesId(int|null $seriesId): void
    {
        $this->seriesId = $seriesId;
    }

    public function getSeriesId(): int|null
    {
        return $this->seriesId;
    }
}
