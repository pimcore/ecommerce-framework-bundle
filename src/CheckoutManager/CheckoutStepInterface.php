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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;

/**
 * Interface for checkout step implementations of online shop framework
 */
interface CheckoutStepInterface
{
    public function __construct(CartInterface $cart, array $options = []);

    /**
     * Returns checkout step name
     *
     */
    public function getName(): string;

    /**
     * Returns saved data of step
     *
     */
    public function getData(): mixed;

    /**
     * Sets delivered data and commits step
     *
     *
     */
    public function commit(mixed $data): bool;
}
