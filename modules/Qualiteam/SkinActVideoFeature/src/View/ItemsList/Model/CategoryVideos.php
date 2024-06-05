<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\ItemsList\Model;

use Qualiteam\SkinActVideoFeature\View\StickyPanel\EducationalVideo\Admin\StickyPanel as StickyPanelEducationalVideo;
use XLite\Core\Request;

class CategoryVideos extends EducationalVideos
{
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['category_videos']);
    }

    protected function getSearchPanelClass()
    {
        return null;
    }

    protected function getFormTarget()
    {
        return 'category_videos';
    }

    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'id' => $this->getCategoryId(),
            ]
        );
    }

    protected function getCreateURL()
    {
        return $this->buildURL(
            'educational_video',
            '',
            [
                'category_id' => $this->getCategoryId(),
            ]
        );
    }

    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{static::PARAM_CATEGORY_ID} = $this->getCategoryId();

        return $result;
    }

    protected function defineColumns()
    {
        $list = parent::defineColumns();
        unset($list['category']);
        foreach ($list as $name => $info) {
            unset($list[$name][static::COLUMN_SORT]);
        }
        return $list;
    }

    protected function getMovePositionWidgetClassName()
    {
        return 'Qualiteam\SkinActVideoFeature\View\FormField\Inline\Input\Text\Position\CategoryVideos\Move';
    }

    protected function getPositionColumnValue(\Qualiteam\SkinActVideoFeature\Model\EducationalVideo $video)
    {
        return $video->getPosition();
    }

    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['id'] = $this->getCategoryId();

        return $this->commonParams;
    }

    protected function getBlankItemsListDescription()
    {
        return static::t('SkinActVideoFeature videos blank');
    }

    protected function getPanelClass()
    {
        return StickyPanelEducationalVideo::class;
    }

    protected function getEmptyListDescription()
    {
        return static::t('SkinActVideoFeature add existing videos from the catalog in the category, or create a new video');
    }

    protected function getCreateButtonLabel()
    {
        return static::t('SkinActVideoFeature new video');
    }

    /**
     * Get current category ID.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return (int) Request::getInstance()->id;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' educational-video-category-add';
    }
}