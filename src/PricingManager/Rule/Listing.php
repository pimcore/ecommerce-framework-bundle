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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule;

use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\RuleInterface;

/**
 * @method Rule[] load()
 * @method Rule|false current()
 * @method \Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Listing\Dao getDao()
 */
class Listing extends \Pimcore\Model\Listing\AbstractListing
{
    protected bool $validate = false;

    public function setValidation(bool $state): void
    {
        $this->validate = (bool)$state;
    }

    public function isValidOrderKey(string $key): bool
    {
        return in_array($key, ['prio', 'name']);
    }

    /**
     * @return RuleInterface[]
     */
    public function getRules(): array
    {
        return $this->getData();
    }

    /**
     * @param RuleInterface[] $rules
     *
     * @return $this
     */
    public function setRules(array $rules): static
    {
        return $this->setData($rules);
    }
}
