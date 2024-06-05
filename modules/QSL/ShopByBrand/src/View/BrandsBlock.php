<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use XCart\Extender\Mapping\ListChild;
use XLite\View\CacheableTrait;
use XLite\Core\Config;

/**
 * @ListChild (list="sidebar.single", zone="customer", weight="105")
 * @ListChild (list="sidebar.first", zone="customer", weight="105")
 */
class BrandsBlock extends \XLite\View\SideBarBox
{
    use CacheableTrait;
    use BrandsBlockTrait;

    public const PARAM_ORDER       = 'order';
    public const PARAM_LIMIT       = 'limit';
    public const PARAM_CATEGORY_ID = 'sbb_category_id';

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'main';
        $result[] = 'category';
        $result[] = 'brand';
        $result[] = 'level_front_page';

        return $result;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/ShopByBrand/brands_block/styles.css';

        return $list;
    }

    /**
     * Get CSS classes for the link wrapper.
     *
     * @param \QSL\ShopByBrand\Model\Brand $brand Brand being displayed
     * @param int                                       $index Index of the brand in the block
     *
     * @return string
     */
    public function getItemClass(\QSL\ShopByBrand\Model\Brand $brand, $index)
    {
        return ($index > 0) ? 'brand' : 'brand first';
    }

    /**
     * Get CSS classes for the link.
     *
     * @param \QSL\ShopByBrand\Model\Brand $brand Brand being displayed
     * @param int                          $index Index of the brand in the block
     *
     * @return string
     */
    public function getLinkClass(\QSL\ShopByBrand\Model\Brand $brand, $index)
    {
        $id = $this->getBrandId();

        return ($id && ($id == $brand->getBrandId())) ? 'active' : 'leaf';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ShopByBrand/brands_block/';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CATEGORY_ID => $this->getCategoryIdParam(),
            static::PARAM_ORDER       => new \XLite\Model\WidgetParam\TypeString(
                'Order',
                Config::getInstance()->QSL->ShopByBrand->shop_by_brand_block_order
            ),
            static::PARAM_LIMIT       => new \XLite\Model\WidgetParam\TypeInt(
                'The maximum number of brands to be displayed',
                Config::getInstance()->QSL->ShopByBrand->shop_by_brand_block_limit
            ),
        ];
    }

    /**
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-top-categories block-brands';
    }

    /**
     * @return string
     */
    protected function getListClasses()
    {
        return 'menu menu-list category-brands';
    }

    /**
     * @return bool
     */
    protected function isVisibleOnThePage()
    {
        if ($this->getTarget() === 'level_front_page') {
            return Config::getInstance()->QSL->ShopByBrand->shop_by_brand_block_lfp;
        }

        return $this->isBrandsBlockOnHomePage()
            ? Config::getInstance()->QSL->ShopByBrand->shop_by_brand_block_home
            : Config::getInstance()->QSL->ShopByBrand->shop_by_brand_block;
    }
}
