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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\Tracker\Analytics;

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\CheckoutCompleteInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\ProductAction;
use Pimcore\Bundle\EcommerceFrameworkBundle\Tracking\Transaction;
use Pimcore\Bundle\GoogleMarketingBundle\Tracker\Tracker as GoogleTracker;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversalEcommerce extends AbstractAnalyticsTracker implements CheckoutCompleteInterface
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'template_prefix' => '@PimcoreEcommerceFramework/Tracking/analytics/universal',
        ]);
    }

    /**
     * Track checkout complete
     *
     */
    public function trackCheckoutComplete(AbstractOrder $order): void
    {
        $transaction = $this->trackingItemBuilder->buildCheckoutTransaction($order);
        $items = $this->trackingItemBuilder->buildCheckoutItems($order);

        $parameters = [];
        $parameters['transaction'] = $transaction;
        $parameters['items'] = $items;
        $parameters['calls'] = $this->buildCheckoutCompleteCalls($transaction, $items);

        $result = $this->renderTemplate('checkout_complete', $parameters);

        $this->tracker->addCodePart($result, GoogleTracker::BLOCK_AFTER_TRACK);
    }

    /**
     * @param ProductAction[] $items
     *
     */
    protected function buildCheckoutCompleteCalls(Transaction $transaction, array $items): array
    {
        $calls = [
            'ecommerce:addTransaction' => [
                $this->transformTransaction($transaction),
            ],
            'ecommerce:addItem' => [],
        ];

        foreach ($items as $item) {
            $calls['ecommerce:addItem'][] = $this->transformProductAction($item);
        }

        return $calls;
    }

    /**
     * Transform transaction into universal data object
     *
     *
     */
    protected function transformTransaction(Transaction $transaction): array
    {
        return $this->filterNullValues(array_merge([
            'id' => $transaction->getId(),                     // Transaction ID. Required.
            'affiliation' => $transaction->getAffiliation() ?: '',      // Affiliation or store name.
            'revenue' => $transaction->getTotal(),                  // Grand Total.
            'shipping' => round($transaction->getShipping(), 2),               // Shipping.
            'tax' => round($transaction->getTax(), 2),                     // Tax.
        ],
            $transaction->getAdditionalAttributes())
        );
    }

    /**
     * Transform product action into universal data object
     *
     *
     */
    protected function transformProductAction(ProductAction $item): array
    {
        return $this->filterNullValues(array_merge([
            'id' => $item->getTransactionId(),                    // Transaction ID. Required.
            'sku' => $item->getId(),                               // SKU/code.
            'name' => $item->getName(),                             // Product name. Required.
            'category' => $item->getCategory(),                         // Category or variation.
            'price' => round($item->getPrice(), 2),                            // Unit price.
            'quantity' => $item->getQuantity() ?: 1,                    // Quantity.
        ], $item->getAdditionalAttributes()));
    }
}
