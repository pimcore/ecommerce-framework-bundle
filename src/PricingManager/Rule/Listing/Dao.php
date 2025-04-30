<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Listing;

use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Listing;

/**
 * @internal
 *
 * @property Listing $model
 */
class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao
{
    protected string $ruleClass = '\Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule';

    public function load(): array
    {
        $rules = [];

        // load objects
        $ruleIds = $this->db->fetchFirstColumn('SELECT id FROM ' . \Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Dao::TABLE_NAME .
                                                 $this->getCondition() . $this->getOrder() . $this->getOffsetLimit());

        foreach ($ruleIds as $id) {
            $rules[] = call_user_func([$this->getRuleClass(), 'getById'], $id);
        }

        $this->model->setRules($rules);

        return $rules;
    }

    public function setRuleClass(string $cartClass): void
    {
        $this->ruleClass = $cartClass;
    }

    public function getRuleClass(): string
    {
        return $this->ruleClass;
    }

    public function getTotalCount(): int
    {
        try {
            return (int) $this->db->fetchOne('SELECT COUNT(*) FROM `' . \Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Rule\Dao::TABLE_NAME . '`' . $this->getCondition());
        } catch (\Exception $e) {
            return 0;
        }
    }
}
