<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

/**
 * Category products controller
 */
class BrandProducts extends \XLite\Controller\Admin\ProductList
{
    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params[] = 'id';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible()
            ? static::t('Manage brand ({{brand_name}})', ['brand_name' => $this->getBrandName()])
            : '';
    }

    /**
     * Return the brand ID
     *
     * @return string
     */
    public function getBrandId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the brand name for the title
     *
     * @return string
     */
    public function getBrand()
    {
        if (is_null($this->brand)) {
            $this->brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
                ->find($this->getBrandId());
        }

        return $this->brand;
    }

    /**
     * Return the brand name for the title
     *
     * @return string
     */
    public function getBrandName()
    {
        /** @var \QSL\ShopByBrand\Model\Brand|null $brand */
        $brand = $this->getBrand();
        return ($brand ? $brand->getName() : '');
    }

    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getBrand()) {
            $this->addLocationNode(
                'Brands',
                $this->buildURL('brands')
            );
        }
    }

    /**
     * @return string
     */
    protected function getLocation()
    {
        return !$this->isVisible()
            ? static::t('No brand defined')
            : (($brandName = $this->getBrandName())
                ? $brandName
                : static::t('Manage brands')
            );
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getBrandId() && $this->getBrand();
    }
}
