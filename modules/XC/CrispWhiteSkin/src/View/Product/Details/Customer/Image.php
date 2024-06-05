<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Image
 * @Extender\Mixin
 */
class Image extends \XLite\View\Product\Details\Customer\Image
{
    /**
     * Return true if image is zoomable
     *
     * @param $image \XLite\Model\Image\Product\Image
     *
     * @return boolean
     */
    protected function isImageZoomable($image)
    {
        return $image->getWidth() > $this->getZoomWidth() || $image->getHeight() > $this->getZoomHeight();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'product/details/parts/cloud-zoom.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/add_to_cart.js';

        return $list;
    }

    /**
     * Get zoom layer width
     *
     * @return integer
     */
    protected function getZoomWidth()
    {
        return \XLite::getController()->getDefaultMaxImageSize(true);
    }

    /**
     * Get zoom layer height
     *
     * @return integer
     */
    protected function getZoomHeight()
    {
        return \XLite::getController()->getDefaultMaxImageSize(false);
    }

    /**
     * Return the max image width depending on whether it is a quicklook popup, or not
     *
     * @return integer
     */
    protected function getWidgetMaxWidth()
    {
        return \XLite::getController()->getDefaultMaxImageSize(true);
    }

    /**
     * Get product image container max height
     *
     * @return boolean
     */
    protected function getWidgetMaxHeight()
    {
        return \XLite::getController()->getDefaultMaxImageSize(false);
    }
}
