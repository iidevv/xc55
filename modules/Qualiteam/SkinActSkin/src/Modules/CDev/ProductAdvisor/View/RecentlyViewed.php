<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\CDev\ProductAdvisor\View;

use XLite\Core\Cache\ExecuteCached;
use XCart\Extender\Mapping\Extender;

/**
 * Recently viewed products widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\ProductAdvisor")
 */
class RecentlyViewed extends \CDev\ProductAdvisor\View\RecentlyViewed
{
    /**
     * Get product list item template.
     *
     * @return string
     */
    public function getProductTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/product.twig';
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        $result = parent::getProductWidgetParams($product);
        $sizes  = $this->getSizes() ?: [0, 0];

        $result[\XLite\View\Product\ListItem::PARAM_ICON_MAX_HEIGHT] = $sizes[1];
        $result[\XLite\View\Product\ListItem::PARAM_ICON_MAX_WIDTH]  = $sizes[0];

        return $result;
    }

    /**
     * Get product images sizes for products grid
     *
     * @return array
     */
    protected function getSizes()
    {
        $cacheParams = [
            get_class($this),
            'getSizes'
        ];

        return ExecuteCached::executeCachedRuntime(function() {
            return \XLite\Logic\ImageResize\Generator::getImageSizes(
                \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT, 'LGThumbnailGrid');
        }, $cacheParams);
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return str_replace("recently-viewed-products","r-viewed-products enlarged-carousel", parent::getListCSSClasses());
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return str_replace(".recently", "",parent::getListName());
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'product';

        return $result;
    }

}
