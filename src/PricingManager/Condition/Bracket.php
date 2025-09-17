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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

class Bracket implements BracketInterface
{
    /**
     * @var ConditionInterface[]
     */
    protected array $conditions = [];

    /**
     * @var list<string|null> BracketInterface::OPERATOR_*
     */
    protected array $operator = [];

    /**
     * @param string|null $operator BracketInterface::OPERATOR_*
     *
     * @return $this
     */
    public function addCondition(ConditionInterface $condition, ?string $operator): static
    {
        $this->conditions[] = $condition;
        $this->operator[] = $operator;

        return $this;
    }

    public function check(EnvironmentInterface $environment): bool
    {
        // A bracket without conditions is not restricted and thus doesn't fail
        if (!$this->conditions) {
            return true;
        }

        // default
        $state = null;

        // check all conditions
        foreach ($this->conditions as $num => $condition) {
            //The first condition shouldn't have an operator.
            //https://github.com/pimcore/pimcore/pull/7902
            $operator = $this->operator[$num];
            if ($num === 0) {
                $operator = null;
            }

            // test condition
            $check = $condition->check($environment);

            // check
            switch ($operator) {
                // first condition
                case null:
                    $state = $check;

                    break;

                    // AND
                case BracketInterface::OPERATOR_AND:
                    if ($check === false) {
                        return false;
                    }
                    //consider current state with check, if not default.
                    $state = $state ?? true;

                    break;

                    // AND FALSE
                case BracketInterface::OPERATOR_AND_NOT:
                    if ($check === true) {
                        return false;
                    }
                    //consider current state with check, if not default.
                    $state = $state ?? true;

                    break;

                    // OR
                case BracketInterface::OPERATOR_OR:
                    if ($check === true) {
                        $state = $check;
                    }

                    break;
            }
        }

        return $state ?? false;
    }

    public function toJSON(): string
    {
        $json = ['type' => 'Bracket', 'conditions' => []];
        foreach ($this->conditions as $num => $condition) {
            $cond = [
                'operator' => $this->operator[$num],
                'condition' => json_decode($condition->toJSON()),
            ];
            $json['conditions'][] = $cond;
        }

        return json_encode($json);
    }

    /**
     *
     * @return $this
     *
     * @throws \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException
     */
    public function fromJSON(string $string): static
    {
        $json = json_decode($string);

        foreach ($json->conditions as $setting) {
            $subcond = Factory::getInstance()->getPricingManager()->getCondition($setting->type);
            $subcond->fromJSON(json_encode($setting));

            $this->addCondition($subcond, $setting->operator);
        }

        return $this;
    }

    /**
     *
     * @return ConditionInterface[]
     */
    public function getConditionsByType(string $typeClass): array
    {
        $conditions = [];

        foreach ($this->conditions as $condition) {
            if ($condition instanceof BracketInterface) {
                $conditions = array_merge($conditions, $condition->getConditionsByType($typeClass));
            } elseif ($condition instanceof $typeClass) {
                $conditions[] = $condition;
            }
        }

        return $conditions;
    }
}
