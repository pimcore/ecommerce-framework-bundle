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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\CartManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartFactory implements CartFactoryInterface
{
    protected array $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['cart_class_name']);

        $resolver->setDefaults([
            'cart_class_name' => Cart::class,
            'guest_cart_class_name' => SessionCart::class,
        ]);

        $resolver->setAllowedTypes('cart_class_name', 'string');
        $resolver->setAllowedTypes('guest_cart_class_name', 'string');
    }

    public function getCartClassName(EnvironmentInterface $environment): string
    {
        if ($environment->getUseGuestCart()) {
            return $this->options['guest_cart_class_name'];
        }

        return $this->options['cart_class_name'];
    }

    public function create(EnvironmentInterface $environment, string $name, ?string $id = null, array $options = []): CartInterface
    {
        $cart = $this->createCartInstance($environment, $name, $id, $options);
        $cart->save();

        return $cart;
    }

    protected function createCartInstance(EnvironmentInterface $environment, string $name, ?string $id = null, array $options = []): CartInterface
    {
        $class = $this->getCartClassName($environment);

        /** @var CartInterface $cart */
        $cart = new $class;

        $cart->setName($name);

        if (null !== $id) {
            $cart->setId($id);
        }

        return $cart;
    }
}
