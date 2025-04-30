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

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Event;

final class IndexServiceEvents
{
    /**
     * Fired when error occurs during processing attributes for index. Event can influence handling of that error (like ignoring, throwing exceptions, etc.)
     *
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\IndexService\PreprocessAttributeErrorEvent")
     *
     * @var string
     */
    const ATTRIBUTE_PROCESSING_ERROR = 'pimcore.ecommerce.indexservice.preProcessAttributeError';

    /**
     * Fired when error occurs during pre processing index data. Event can influence handling of that error (like throwing exceptions, etc.)
     *
     * @Event("Pimcore\Bundle\EcommerceFrameworkBundle\Event\Model\IndexService\PreprocessErrorEvent")
     *
     * @var string
     */
    const GENERAL_PREPROCESSING_ERROR = 'pimcore.ecommerce.indexservice.generalPreProcessingError';
}
