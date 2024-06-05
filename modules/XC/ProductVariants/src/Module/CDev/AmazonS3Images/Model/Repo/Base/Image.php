<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\CDev\AmazonS3Images\Model\Repo\Base;

use XCart\Extender\Mapping\Extender;

/**
 * Base image model extension
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\AmazonS3Images")
 */
abstract class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Get managed image repositories
     *
     * @return array
     */
    public static function getManagedRepositories()
    {
        $result = parent::getManagedRepositories();
        $result[] = 'XC\ProductVariants\Model\Image\ProductVariant\Image';

        return $result;
    }
}
