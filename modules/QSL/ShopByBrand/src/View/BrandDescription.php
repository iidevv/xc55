<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Brand page - description
 *
 * @ListChild (list="center", zone="customer")
 */
class BrandDescription extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'brand';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/ShopByBrand/brand_page/description_style.less';

        return $list;
    }

    /**
     * Get the product's brand description.
     *
     * @return string
     */
    public function getBrandDescription()
    {
        return $this->getBrand()->getViewDescription();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ShopByBrand/brand_page/description.twig';
    }

    /**
     * Check widget visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getBrand();
    }
}
