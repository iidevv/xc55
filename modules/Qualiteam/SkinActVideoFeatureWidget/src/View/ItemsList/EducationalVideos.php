<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeatureWidget\View\ItemsList;

use Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class EducationalVideos extends \Qualiteam\SkinActVideoFeature\View\ItemsList\EducationalVideos
{
    const PARAM_SHOW_ALL_VIDEOS = 'show_all_videos';

    protected function isShowSubcategories()
    {
        return parent::isShowSubcategories()
            && !$this->isShowAllVideos();
    }

    protected function isShowAllVideos()
    {
        return Request::getInstance()->show_all_videos;
    }

    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        if ($this->isShowAllVideos()) {
            $cnd->{EducationalVideo::P_CATEGORY_ID} = static::PARAM_SHOW_ALL_VIDEOS;
        }

        return $cnd;
    }
}