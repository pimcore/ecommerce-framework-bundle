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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tracking;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

abstract class Tracker implements TrackerInterface
{
    protected TrackingItemBuilderInterface $trackingItemBuilder;

    protected Environment $twig;

    protected string $templatePrefix;

    protected array $assortmentTenants;

    protected array $checkoutTenants;

    /**
     * Tracker constructor.
     *
     */
    public function __construct(
        TrackingItemBuilderInterface $trackingItemBuilder,
        Environment $twig,
        array $options = [],
        array $assortmentTenants = [],
        array $checkoutTenants = []
    ) {
        $this->trackingItemBuilder = $trackingItemBuilder;
        $this->twig = $twig;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->processOptions($resolver->resolve($options));

        $this->assortmentTenants = $assortmentTenants;
        $this->checkoutTenants = $checkoutTenants;
    }

    protected function processOptions(array $options): void
    {
        $this->templatePrefix = $options['template_prefix'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['template_prefix']);

        $resolver->setAllowedTypes('template_prefix', 'string');
    }

    protected function getTemplatePath(string $name): string
    {
        return sprintf(
            '%s/%s.js.twig',
            $this->templatePrefix,
            $name
        );
    }

    protected function renderTemplate(string $name, array $parameters): string
    {
        return $this->twig->render(
            $this->getTemplatePath($name),
            $parameters
        );
    }

    /**
     * Remove null values from an object, keep protected keys in any case
     *
     *
     */
    protected function filterNullValues(array $data, array $protectedKeys = []): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $isProtected = in_array($key, $protectedKeys);
            if (null !== $value || $isProtected) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getAssortmentTenants(): array
    {
        return $this->assortmentTenants;
    }

    public function getCheckoutTenants(): array
    {
        return $this->checkoutTenants;
    }
}
