<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Product\Details;

/**
 * Widget promoting reward points which customers will earn after purchasing the product.
 *
 * Widget requires the "product" parameter that should be passed from a parent widget.
 * That's why we use ListChild in an extra template that just inserts this widget.
 */
class Brand extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Cached brand model.
     *
     * @var \QSL\ShopByBrand\Model\Brand
     */
    protected $brand;

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
     * Check if the product has an associated brand.
     *
     * @return bool
     */
    public function hasBrand()
    {
        return !is_null($this->getBrand());
    }

    /**
     * Get the brand model for the product.
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function getBrand()
    {
        if (!isset($this->brand)) {
            $this->brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
                ->findProductBrand($this->getProduct());
        }

        return $this->brand;
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
     * @return \XLite\Model\Image
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
        return 100;
    }

    /**
     * Get the width of the product's brand logo.
     *
     * @return int
     */
    public function getLogoHeight()
    {
        return 60;
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-brand';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/ShopByBrand/brand/brand.css';

        return $list;
    }

    /**
     * Get relative path to the default widget template.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ShopByBrand/brand/icon.twig';
    }
}
