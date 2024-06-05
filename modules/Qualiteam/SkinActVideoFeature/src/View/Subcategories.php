<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View;

use Doctrine\Common\Collections\ArrayCollection;
use Qualiteam\SkinActVideoFeature\Model\VideoCategory;

/**
 * Subcategories list
 */
class Subcategories extends \XLite\View\Dialog
{
    /**
     * Widget parameter names
     */
    public const PARAM_DISPLAY_MODE = 'displayMode';
    public const PARAM_ICON_MAX_WIDTH = 'iconWidth';
    public const PARAM_ICON_MAX_HEIGHT = 'iconHeight';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'educational_videos';

        return $result;
    }

    /**
     * Get image alternative text
     *
     * @param \Qualiteam\SkinActVideoFeature\Model\Image $image Image
     *
     * @return string
     */
    protected function getAlt($image)
    {
        return $image
            ? $image->getAlt() ?: $image->getCategory()->getName()
            : '';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActVideoFeature/items_list/educational_videos/subcategories';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isCategoryVisible()
            && $this->hasSubcategories();
    }

    /**
     * Widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ICON_MAX_WIDTH => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon width',
                \XLite::getController()->getDefaultMaxImageSize(
                    true,
                    \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                    'Default'
                ),
                true
            ),
            static::PARAM_ICON_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon height',
                \XLite::getController()->getDefaultMaxImageSize(
                    false,
                    \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                    'Default'
                ),
                true
            ),
        ];
    }

    /**
     * Return the maximal icon width
     *
     * @return integer
     */
    protected function getIconWidth()
    {
        return $this->getParam(static::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * Return the maximal icon height
     *
     * @return integer
     */
    protected function getIconHeight()
    {
        return $this->getParam(static::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * Check for subcategories
     *
     * @return boolean
     */
    protected function hasSubcategories()
    {
        return $this->getCategory() ? $this->getCategory()->hasSubcategories() : false;
    }

    /**
     * Return subcategories
     *
     * @return ArrayCollection
     */
    protected function getSubcategories()
    {
        return $this->getCategory() ? $this->getCategory()->getSubcategories() : new ArrayCollection();
    }

    protected function isSubcategoryHasChild(VideoCategory $category)
    {
        return $category->hasSubcategories();
    }

    /**
     * Check if the category is visible
     *
     * @return boolean
     */
    protected function isCategoryVisible()
    {
        return $this->getCategory() ? $this->getCategory()->isVisible() : false;
    }

    // }}}

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return 'subcategories__block';
    }
}