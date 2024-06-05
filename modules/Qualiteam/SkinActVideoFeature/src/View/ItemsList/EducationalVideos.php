<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\ItemsList;

use Qualiteam\SkinActVideoFeature\Model\EducationalVideo;
use XLite\Core\Request as Request;
use XLite\View\Pager\Infinity;

class EducationalVideos extends AEducationalVideos
{
    const PARAM_VIDEOSUBSTRING   = 'videosubstring';
    const WIDGET_TARGET = 'educational_videos';

    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = static::WIDGET_TARGET;

        return $result;
    }

    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/js/script.js';

        return $list;
    }

    public static function getSearchParams()
    {
        return [
            \Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo::P_VIDEO_DESCRIPTION => static::PARAM_VIDEOSUBSTRING,
        ];
    }

    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_VIDEOSUBSTRING;
    }

    protected function defineWidgetSignificantArguments()
    {
        return [
            static::PARAM_VIDEOSUBSTRING   => $this->getVideosubstring(),
        ];
    }

    protected function getVideosubstring()
    {
        return $this->getParam(static::PARAM_VIDEOSUBSTRING) ?? \XLite\Core\Session::getInstance()->{$this->getSessionCell()}[static::PARAM_VIDEOSUBSTRING];
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_VIDEOSUBSTRING => new \XLite\Model\WidgetParam\TypeString(
                'Video substring',
                null,
                false
            ),
        ];
    }

    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $categoryId = Request::getInstance()->category_id;

        if ($categoryId && !$this->isRootCategory()) {
            $cnd->{\Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo::P_CATEGORY_ID} = $categoryId;
        }

        return $cnd;
    }

    protected function isPagerVisible()
    {
        return 0 < $this->getItemsCount();
    }

    protected function getPagerClass()
    {
        return Infinity::class;
    }

    protected function defineRepositoryName()
    {
        return EducationalVideo::class;
    }

    protected function getListName()
    {
        return parent::getListName() . '.educational-videos';
    }

    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-educational-videos';
    }

    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    protected function isShowVideoHead()
    {
        return !$this->isRootCategory() && !$this->isSearchCondition();
    }

    protected function isHeaderVisible()
    {
        return false;
    }

    protected function isShowSubcategories()
    {
        return $this->isRootCategory() && !$this->isSearchCondition();
    }

    protected function isSearchCondition()
    {
        return Request::getInstance()->videosubstring ?? null;
    }

    protected function isRootCategory()
    {
        return $this->getCategory()->getDepth() === -1;
    }

    protected function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    protected function getWidgetTagAttributes()
    {
        $data = parent::getWidgetTagAttributes();

        if ($this->isShowSubcategories()) {
            $data['data-carousel'] = 1;
        }

        return $data;
    }
}