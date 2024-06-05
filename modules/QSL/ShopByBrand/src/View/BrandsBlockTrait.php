<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XLite\Core\Database;
use XLite\Core\Config;
use XLite\Core\Auth;
use XLite\Core\Request;

trait BrandsBlockTrait
{
    /**
     * @var array
     */
    protected $brands;

    /**
     * Check whether the "See all brands" link should be visible in the block.
     *
     * @return bool
     */
    public function isAllBrandsLinkVisible()
    {
        return Database::getRepo('QSL\ShopByBrand\Model\Brand')
                ->countEnabledBrands() > $this->getParam(self::PARAM_LIMIT);
    }

    /**
     * @return string
     */
    protected function getHead()
    {
        return static::t('Brands');
    }

    /**
     * Return list of brands.
     *
     * @return array Returns array(array(0=>$brand, 'productCount'=>$productCount)).
     */
    protected function getBrands()
    {
        if (!isset($this->brands)) {
            $this->brands = Database::getRepo('QSL\ShopByBrand\Model\Brand')
                ->getCategoryBrandsWithProductCount(
                    $this->getCategoryId(),
                    (bool) Config::getInstance()->QSL->ShopByBrand->hide_brands_without_products,
                    $this->getParam(self::PARAM_LIMIT),
                    $this->getParam(self::PARAM_ORDER)
                );
        }

        return $this->brands;
    }

    /**
     * @return \XLite\Model\WidgetParam\ObjectId\Category
     */
    protected function getCategoryIdParam()
    {
        $categoryId = (int) Request::getInstance()->category_id;

        return new \XLite\Model\WidgetParam\ObjectId\Category(
            'Category ID',
            ($categoryId > 1) ? $categoryId : 0,
            true,
            true
        );
    }

    /**
     * @return int
     */
    protected function getCategoryId()
    {
        return $this->getParam(static::PARAM_CATEGORY_ID);
    }

    /**
     * @return int
     */
    protected function getBrandId()
    {
        return (int) \XLite\Core\Request::getInstance()->brand_id;
    }

    /**
     * Check if the list should display number of products per brand.
     *
     * @return bool
     */
    protected function isProductCountVisible()
    {
        return false;
    }

    /**
     * @return array
     */
    protected function getCacheParameters()
    {
        $list   = parent::getCacheParameters();
        $list[] = $this->getCategoryId();
        $list[] = $this->getBrandId();
        $list[] = Auth::getInstance()->isLogged() && Auth::getInstance()->getProfile()->getMembership()
            ? Auth::getInstance()->getProfile()->getMembership()->getMembershipId()
            : '-';

        return $list;
    }

    /**
     * Checks if the widget should render its content.
     *
     * @return bool
     */
    protected function isWidgetVisible()
    {
        return $this->isVisibleOnThePage() && $this->getBrands();
    }

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        $this->isWidgetVisible() ? parent::doDisplay($template) : false;
    }

    /**
     * Check if it is the home page.
     *
     * @return bool
     */
    protected function isBrandsBlockOnHomePage()
    {
        return \XLite::getController()->getTarget() == \XLite::TARGET_DEFAULT;
    }
}
