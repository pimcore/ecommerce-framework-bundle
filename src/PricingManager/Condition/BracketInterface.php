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

use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;

interface BracketInterface extends ConditionInterface
{
    const OPERATOR_AND = 'and';

    const OPERATOR_OR = 'or';

    const OPERATOR_AND_NOT = 'and_not';

    /**
     * @param string $operator BracketInterface::OPERATOR_*
     *
     * @return $this
     */
    public function addCondition(ConditionInterface $condition, string $operator): static;

    /**
     * Returns all defined conditions with given type
     *
     *
     * @return ConditionInterface[]
     */
    public function getConditionsByType(string $typeClass): array;
}
