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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Tests\Ecommerce\DependencyInjection\Config\Processor;

use Pimcore\Bundle\EcommerceFrameworkBundle\DependencyInjection\Config\Processor\TenantProcessor;
use Pimcore\Tests\Support\Test\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class TenantProcessorTest extends TestCase
{
    private TenantProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new TenantProcessor();
    }

    public function testNothingIsMergedWithoutDefaults(): void
    {
        $input = [
            'tenant1' => [
                'foo' => 'bar',
                'baz' => [
                    'in',
                    'ga',
                ],
            ],
        ];

        $this->assertEquals($input, $this->processor->mergeTenantConfig($input));
    }

    public function testDefaultValuesAreMergedIntoEveryTenantAndRemoved(): void
    {
        $input = [
            '_defaults' => [
                'default' => 'value',
            ],
            'default' => [
                'foo' => 'bar',
            ],
            'tenant1' => [
                'baz' => [
                    'in',
                    'ga',
                ],
            ],
        ];

        $expected = [
            'default' => [
                'default' => 'value',
                'foo' => 'bar',
            ],
            'tenant1' => [
                'default' => 'value',
                'baz' => [
                    'in',
                    'ga',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->processor->mergeTenantConfig($input));
    }

    /**
     * Additional defaults can be used for YAML inheritance, but are removed
     * from final config.
     */
    public function testAdditionalDefaultsAreRemovedWithoutMerging(): void
    {
        $input = [
            '_defaults' => [
                'default' => 'value',
            ],
            '_defaults_foobar' => [
                'xy' => 'z',
            ],
            '_defaultsblahfoo' => [
                'blah' => 'foo',
            ],
            'tenant1' => [
                'foo' => 'bar',
            ],
            'tenant2' => [
                'baz' => [
                    'in',
                    'ga',
                ],
            ],
        ];

        $expected = [
            'tenant1' => [
                'default' => 'value',
                'foo' => 'bar',
            ],
            'tenant2' => [
                'default' => 'value',
                'baz' => [
                    'in',
                    'ga',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->processor->mergeTenantConfig($input));
    }

    public function testAssociativeArraysAreExtended(): void
    {
        $input = [
            '_defaults' => [
                'values' => [
                    'A' => 'B',
                    'C' => 'D',
                ],
            ],
            'tenant1' => [
                'values' => [
                    'A' => 'B1',
                ],
            ],
            'tenant2' => [
                'values' => [
                    'E' => 'F',
                ],
            ],
        ];

        $expected = [
            'tenant1' => [
                'values' => [
                    'A' => 'B1',
                    'C' => 'D',
                ],
            ],
            'tenant2' => [
                'values' => [
                    'A' => 'B',
                    'C' => 'D',
                    'E' => 'F',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->processor->mergeTenantConfig($input));
    }

    public function testSequentialArraysAreMerged(): void
    {
        $input = [
            '_defaults' => [
                'values' => ['A', 'B', 'C'],
            ],
            'tenant1' => [
                'values' => ['D', 'E'],
            ],
            'tenant2' => [
                'values' => ['F'],
            ],
        ];

        $expected = [
            'tenant1' => [
                'values' => ['A', 'B', 'C', 'D', 'E'],
            ],
            'tenant2' => [
                'values' => ['A', 'B', 'C', 'F'],
            ],
        ];

        $this->assertEquals($expected, $this->processor->mergeTenantConfig($input));
    }

    public function testDefaultsAreDeepMerged(): void
    {
        $input = [
            '_defaults' => [
                'level1' => [
                    'level11A' => [
                        'foo',
                        'bar',
                    ],
                    'level11B' => [
                        'x' => 'yz',
                        'y' => 'z',
                    ],
                ],
            ],

            'tenant1' => [
                'level1' => [
                    'level11B' => [
                        'y' => 'AA',
                    ],
                ],
                'level2' => [
                    'foo' => ['bar', 'bazinga'],
                ],
            ],

            'tenant2' => [
                'level1' => [
                    'level11A' => [
                        'bazinga',
                    ],
                    'level11C' => [
                        'my' => 'custom element',
                    ],
                ],
                'level2' => 'ABC',
            ],
        ];

        $expected = [
            'tenant1' => [
                'level1' => [
                    'level11A' => [
                        'foo',
                        'bar',
                    ],
                    'level11B' => [
                        'x' => 'yz',
                        'y' => 'AA',
                    ],
                ],
                'level2' => [
                    'foo' => ['bar', 'bazinga'],
                ],
            ],

            'tenant2' => [
                'level1' => [
                    'level11A' => [
                        'foo',
                        'bar',
                        'bazinga',
                    ],
                    'level11B' => [
                        'x' => 'yz',
                        'y' => 'z',
                    ],
                    'level11C' => [
                        'my' => 'custom element',
                    ],
                ],
                'level2' => 'ABC',
            ],
        ];

        $this->assertEquals($expected, $this->processor->mergeTenantConfig($input));
    }

    public function testExceptionOnMismatchingTypes(): void
    {
        $input = [
            '_defaults' => [
                'values' => ['A', 'B', 'C'],
            ],
            'tenant1' => [
                'values' => 'D;E',
            ],
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->processor->mergeTenantConfig($input);
    }
}
