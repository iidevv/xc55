<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\Model\Repo\Image\Product;

use XLite\Model\Repo\Base\Image;

class FullWidthImage extends Image
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'full_width_images';
    }

    protected function defineStorageRepositories()
    {
        $list = parent::defineStorageRepositories();

        $list[] = \Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage::class;

        return $list;
    }
}
