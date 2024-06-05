<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Gallery
 * @Extender\Mixin
 */
class Gallery extends \XLite\View\Product\Details\Customer\Gallery
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/details_gallery.js';
        $list[] = 'js/cycle2/jquery.cycle2.min.js';
        $list[] = 'js/cycle2/jquery.cycle2.carousel.min.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => $this->getDir() . '/parts/gallery_visible.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @return array
     */
    protected function getDefaultCycleData()
    {
        return [
            'cycle-fx' => 'carousel',
            'cycle-timeout' => 0,
            'cycle-manual-speed' => 300,
            'cycle-log' => false,
            'cycle-allow-wrap' => false,
            'cycle-auto-height' => false,
            'cycle-auto-init' => false,
        ];
    }

    /**
     * Returns the minimal count of product images to trigger slider mode
     *
     * @return integer
     */
    protected function getMinCountForSlider()
    {
        return 4;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getCSSClasses()
    {
        return '';
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
