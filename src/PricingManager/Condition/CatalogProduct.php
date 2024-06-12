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

use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;
use Pimcore\Model\DataObject\Concrete;

class CatalogProduct extends AbstractObjectListCondition implements CatalogProductInterface
{
    /**
     * @var AbstractProduct[]
     *
     * @deprecated Will be internal in Pimcore 12
     */
    protected array $products = [];

    /**
     * Serialized product IDs
     *
     * @deprecated Will be internal in Pimcore 12
     */
    protected array $productIds = [];

    public function check(EnvironmentInterface $environment): bool
    {
        // init
        $productsPool = [];

        // get current product if we have one
        if ($environment->getProduct()) {
            $productsPool[] = $environment->getProduct();
        }

        // products from cart
        if ($environment->getExecutionMode() === EnvironmentInterface::EXECUTION_MODE_CART && $environment->getCart()) {
            foreach ($environment->getCart()->getItems() as $item) {
                $productsPool[] = $item->getProduct();
            }
        }

        // test
        foreach ($productsPool as $currentProduct) {
            /** @var Concrete $currentProductCheck */
            $currentProductCheck = $currentProduct;
            while ($currentProductCheck instanceof CheckoutableInterface) {
                if (in_array($currentProductCheck->getId(), $this->productIds)) {
                    return true;
                }
                $currentProductCheck = $currentProductCheck->getParent();
            }
        }

        return false;
    }

    public function toJSON(): string
    {
        // basic
        $json = [
            'type' => 'CatalogProduct',
            'products' => [],
        ];

        // add categories
        foreach ($this->getProducts() as $product) {
            $json['products'][] = [
                $product->getId(),
                $product->getFullPath(),
            ];
        }

        return json_encode($json);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $products = [];
        foreach ($json->products as $cat) {
            $product = $this->loadObject($cat->id);
            if ($product) {
                $products[] = $product;
            }
        }
        $this->setProducts($products);

        return $this;
    }

    /**
     * Don't cache the entire product object
     *
     *
     * @internal
     */
    public function __sleep(): array
    {
        if (isset($this->products)) {
            return $this->handleSleep('products', 'productIds');
        }

        return ['productIds'];
    }

    /**
     * Lazily restore products from serialized ID list {@see __get()}
     */
    public function __wakeup(): void
    {
        unset($this->products);
    }

    /**
     * @param AbstractProduct[] $products
     *
     */
    public function setProducts(array $products): CatalogProductInterface
    {
        $this->products = $products;
        $this->productIds = array_map(fn ($product) => $product->getId(), $products);

        return $this;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * This lazily initializes the "products" property in a backwards compatible way.
     *
     * @todo: move the lazy initialization into {@see getProducts()} for Pimcore 12
     */
    public function &__get(string $name): mixed
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        if ('products' !== $name) {
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $this::class,
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line'],
                ),
                \E_USER_WARNING,
            );

            $result = null;

            return $result;
        }

        // verify that access to lazy properties is not happening from outside allowed scopes
        $caller = $backtrace[1]['class'];
        if (!($caller === $this::class
            || is_subclass_of($caller, $this::class)
            || $caller === \ReflectionProperty::class
            || is_subclass_of($caller, \ReflectionProperty::class)
        )) {
            throw new \Error(sprintf(
                'Cannot access protected property %s::$%s in %s:%s',
                $this::class,
                $name,
                $backtrace[1]['file'],
                $backtrace[1]['line'],
            ));
        }

        $this->products = [];
        $this->handleWakeup('products', 'productIds');

        return $this->products;
    }
}
