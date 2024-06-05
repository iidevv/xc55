<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\Controller\Admin;


class ImagesForColors extends \XLite\Controller\Admin\AAdmin
{

    protected function doActionUpdateItemsList()
    {
        $list = new \Qualiteam\SkinActImagesForColors\View\ItemsList\Model\ImagesForColors();

        $list->processQuick();
    }
}