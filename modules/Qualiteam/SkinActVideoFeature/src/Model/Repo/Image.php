<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model\Repo;

class Image extends \XLite\Model\Repo\Base\Image
{
    public function getStorageName()
    {
        return 'video_category';
    }
}