<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\ItemsList;

abstract class AEducationalVideos extends \XLite\View\ItemsList\AItemsList
{
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();

        /** @TODO change owl carousel js */
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/lib/owl.carousel.js';

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/js/jquery.lazytube.js';

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/items_list/controller.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();

        /** @TODO change owl carousel css */
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/style/owl.carousel.css';
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/style/owl.theme.css';
        $list[] = $this->getDir() . '/style.less';

        $list[] = [
            'file'  => 'modules/Qualiteam/SkinActVideoFeature/style/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActVideoFeature/items_list';
    }

    protected function getPageBodyDir()
    {
        return 'educational_videos';
    }
}