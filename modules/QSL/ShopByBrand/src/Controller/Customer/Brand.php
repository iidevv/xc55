<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Customer;

class Brand extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Cached number of products the brand has.
     *
     * @var integer
     */
    protected $productCount;

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return \QSL\ShopByBrand\Controller\Customer\Brand
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->params[] = 'brand_id';
    }

    /**
     * Check if current page is accessible.
     *
     * @return bool
     */
    public function checkAccess()
    {
        return $this->getBrand() && parent::checkAccess();
    }

    public function isVisible()
    {
        return $this->getBrand()->getEnabled();
    }

    /**
     * Get the brand that we are rendering the page for.
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function getBrand()
    {
        return $this->getBrandId()
            ? \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->find($this->getBrandId())
            : null;
    }

    /**
     * Return the page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return ($this->checkAccess() && $this->isVisible())
            ? $this->getBrand()->getName()
            : '';
    }

    /**
     * Returns the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        $model = $this->getModelObject();

        return ($model && $model->getMetaTitle()) ? $model->getMetaTitle() : $this->getTitle();
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $brand = $this->getBrand();

        return $brand
            ? ($brand->getMetaDescription()) ?: strip_tags(str_replace('&nbsp;', ' ', str_replace("\n", ' ', $brand->getDescription())))
            : parent::getMetaDescription();
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        $brand = $this->getBrand();

        return $brand ? $brand->getMetaKeywords() : parent::getKeywords();
    }

    /**
     * Return the model that we are rendering the page for.
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function getModelObject()
    {
        return $this->getBrand();
    }

    /**
     * Common method to determine current location.
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->checkAccess()
            ? $this->getBrand()->getName()
            : 'Page not found';
    }

    /**
     * Add part to the location nodes list
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->locationPath[] = new \QSL\ShopByBrand\View\Location\Node\Brands();
    }

    /**
     * Return the ID of the brand that we are rendering the page for.
     *
     * @return int
     */
    protected function getBrandId()
    {
        return (int) \XLite\Core\Request::getInstance()->brand_id;
    }

    /**
     * Check if redirect to clean URL is needed
     *
     * @return bool
     */
    protected function isRedirectToCleanURLNeeded()
    {
        return parent::isRedirectToCleanURLNeeded()
            || (!\XLite::isCleanURL() && $this->getModelObject()->getCleanURL());
    }

    /**
     * Check if the brand has products.
     *
     * @return bool
     */
    protected function hasBrandProducts()
    {
        return $this->countBrandProducts() > 0;
    }

    /**
     * Get the number of products the brand has.
     *
     * @return bool
     */
    protected function countBrandProducts()
    {
        if (!isset($this->productCount)) {
            $cnd = new \XLite\Core\CommonCell();

            $cnd->{\XLite\Model\Repo\Product::P_BRAND_ID} = $this->getBrandId();
            if (\XLite\Core\Config::getInstance()->General->show_out_of_stock_products !== 'directLink') {
                $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = \XLite\Model\Repo\Product::INV_ALL;
            }

            $this->productCount = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, true);
        }

        return $this->productCount;
    }
}
