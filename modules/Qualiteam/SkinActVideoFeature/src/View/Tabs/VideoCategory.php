<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Tabs;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Database;

/**
 * Tabs related to video category section
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class VideoCategory extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'video_categories';
        if (\XLite\Core\Request::getInstance()->id) {
            $list[] = 'video_category';
            $list[] = 'category_videos';
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'video_category' => [
                'weight'   => 100,
                'title'    => static::t('SkinActVideoFeature info'),
                'template' => 'modules/Qualiteam/SkinActVideoFeature/tabs/body.twig',
            ],
            'video_categories' => [
                'weight'   => 200,
                'title'    => static::t('SkinActVideoFeature subcategories'),
                'widget'    => 'Qualiteam\SkinActVideoFeature\View\ItemsList\Model\VideoCategories',
            ],
            'category_videos' => [
                'weight'   => 300,
                'title'    => static::t('SkinActVideoFeature educational videos'),
                'widget'    => 'Qualiteam\SkinActVideoFeature\View\ItemsList\Model\CategoryVideos',
            ],
        ];
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        if (
            !\XLite\Core\Request::getInstance()->id
            && !\XLite\Core\Request::getInstance()->parent
        ) {
            // Front page
            unset($this->tabs['video_category'], $this->tabs['category_videos']);
        } elseif (!\XLite\Core\Request::getInstance()->id) {
            // New category
            unset($this->tabs['video_categories'], $this->tabs['category_videos']);
        } elseif (\XLite\Core\Request::getInstance()->id) {
            $category = Database::getRepo(VideoCategoryModel::class)
                ->findOneBy(['id' => \XLite\Core\Request::getInstance()->id]);

            if($category->isSecondLevelSubcategory()) {
                unset($this->tabs['video_categories']);
            }
        }

        return parent::prepareTabs();
    }

    /**
     * Returns an URL to a tab
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL($target, '', ['id' => \XLite\Core\Request::getInstance()->id]);
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return bool
     */
    protected function isTabsNavigationVisible()
    {
        $visible = parent::isTabsNavigationVisible();

        if (
            !\XLite\Core\Request::getInstance()->id
            && !\XLite\Core\Request::getInstance()->parent
        ) {
            $visible = false;
        }

        return $visible;
    }
}