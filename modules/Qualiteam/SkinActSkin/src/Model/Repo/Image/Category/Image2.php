<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Model\Repo\Image\Category;

/**
 * Category
 */
class Image2 extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'category2';
    }
}
