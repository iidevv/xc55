<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model\Repo\Image;

/**
 * Swatch
 */
class Swatch extends \XLite\Model\Repo\Base\Image
{
    /**
     * @inheritdoc
     */
    public function getStorageName()
    {
        return 'color_swatch_images';
    }
}
