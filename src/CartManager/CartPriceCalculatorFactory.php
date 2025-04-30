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

class CartPriceCalculatorFactory implements CartPriceCalculatorFactoryInterface
{
    protected EnvironmentInterface $environment;

    protected array $modificatorConfig;

    protected array $options;

    public function __construct(array $modificatorConfig, array $options = [])
    {
        $this->modificatorConfig = $modificatorConfig;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('class');

        $resolver->setDefaults([
            'class' => CartPriceCalculator::class,
        ]);

        $resolver->setAllowedTypes('class', 'string');
    }

    public function create(EnvironmentInterface $environment, CartInterface $cart): CartPriceCalculatorInterface
    {
        $class = $this->options['class'];

        return new $class($environment, $cart, $this->modificatorConfig);
    }
}
