<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model\Repo\Base;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract storage repository
 * @Extender\Mixin
 */
abstract class Storage extends \XLite\Model\Repo\Base\Storage
{
    /**
     * Define all storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $list = parent::defineStorageRepositories();

        $list[] = 'QSL\ColorSwatches\Model\Image\Swatch';

        return $list;
    }
}
