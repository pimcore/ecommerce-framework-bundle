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
