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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager;

interface RuleInterface
{
    public function getId(): ?int;

    public function setId(?int $id): static;

    public function setName(string $name): static;

    public function getName(): string;

    /**
     *
     * @return $this
     */
    public function setLabel(string $label, ?string $locale = null): static;

    public function getLabel(?string $locale = null): string;

    /**
     *
     * @return $this
     */
    public function setDescription(string $description, ?string $locale = null): static;

    public function getDescription(?string $locale = null): ?string;

    public function setCondition(ConditionInterface $condition): static;

    public function getCondition(): ?ConditionInterface;

    public function setActions(array $action): static;

    /**
     * @return ActionInterface[]
     */
    public function getActions(): array;

    public function setActive(bool $active): static;

    public function getActive(): bool;

    public function setBehavior(string $behavior): static;

    public function getBehavior(): string;

    /**
     * test all conditions if this rule is valid
     *
     *
     */
    public function check(EnvironmentInterface $environment): bool;

    /**
     * checks if rule has at least one action that changes product price (and not cart price)
     *
     */
    public function hasProductActions(): bool;

    /**
     * checks if rule has at least one action that changes cart price
     *
     */
    public function hasCartActions(): bool;

    /**
     * execute rule actions based on current product
     *
     *
     * @return $this
     */
    public function executeOnProduct(EnvironmentInterface $environment): static;

    /**
     * execute rule actions based on current cart
     *
     *
     */
    public function executeOnCart(EnvironmentInterface $environment): RuleInterface;

    /**
     *
     * @return ConditionInterface[]
     */
    public function getConditionsByType(string $typeClass): array;

    public function setPrio(int $prio): static;

    public function getPrio(): int;

    public function save(): static;

    /**
     * delete item
     */
    public function delete(): void;
}
