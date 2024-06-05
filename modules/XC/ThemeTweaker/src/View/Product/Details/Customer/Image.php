<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Image
 * @Extender\Mixin
 */
abstract class Image extends \XLite\View\Product\Details\Customer\Image
{
    /**
     * Check if the product has any image to ZOOM
     *
     * @return boolean
     */
    protected function hasZoomImage()
    {
        return static::isInPreviewMode() ? false : parent::hasZoomImage();
    }

    /**
     * Enables inline editing mode if current page is a product preview.
     *
     * @return boolean
     */
    public static function isInPreviewMode()
    {
        $controller = \XLite::getController();
        return $controller instanceof \XLite\Controller\Customer\Product
            && $controller->isPreview()
            && !ThemeTweaker::getInstance()->isInLayoutMode();
    }
}
