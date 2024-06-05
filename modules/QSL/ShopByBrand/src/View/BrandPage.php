<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Brand page widget.
 *
 * @ListChild (list="center", zone="customer")
 */
class BrandPage extends \XLite\View\AView
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
        $list[] = 'modules/QSL/ShopByBrand/brand_page/styles.css';

        return $list;
    }

    /**
     * Check if the product's brand has the logo.
     *
     * @return bool
     */
    public function hasBrandLogo()
    {
        return !is_null($this->getLogo());
    }

    /**
     * Get the product's brand logo.
     *
     * @return \QSL\ShopByBrand\Model\Image\Brand\Image
     */
    public function getLogo()
    {
        return $this->getBrand()->getImage();
    }

    /**
     * Get the width of the product's brand logo.
     *
     * @return int
     */
    public function getLogoWidth()
    {
        return 240;
    }

    /**
     * Get the width of the product's brand logo.
     *
     * @return int
     */
    public function getLogoHeight()
    {
        return 0;
    }

    /**
     * Get the name of the product brand.
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->getBrand()->getName();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ShopByBrand/brand_page/body.twig';
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

    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' brand-block-wrapper';
    }
}
