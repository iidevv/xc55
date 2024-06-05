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
 * @ListChild (list="center.bottom", zone="customer", weight="110")
 */
class BrandsDialog extends \XLite\View\Dialog
{
    use CacheableTrait;
    use BrandsBlockTrait;

    public const PARAM_ORDER           = 'order';
    public const PARAM_LIMIT           = 'limit';
    public const PARAM_CATEGORY_ID     = 'sbb_category_id';
    public const PARAM_DISPLAY_MODE    = 'displayMode';
    public const PARAM_ICON_MAX_WIDTH  = 'iconWidth';
    public const PARAM_ICON_MAX_HEIGHT = 'iconHeight';

    public const DISPLAY_MODE_ICONS = 'icons';
    public const DISPLAY_MODE_LIST  = 'list';
    public const DISPLAY_MODE_HIDE  = 'hide';

    /**
     * @var array
     */
    protected $displayModes = [
        self::DISPLAY_MODE_LIST  => 'List',
        self::DISPLAY_MODE_ICONS => 'Icons',
        self::DISPLAY_MODE_HIDE  => 'Hide',
    ];

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'main';
        $result[] = 'category';
        $result[] = 'level_front_page';

        return $result;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'common/grid-list.css';
        $list[] = 'modules/QSL/ShopByBrand/brands_dialog/styles.less';

        return $list;
    }

    /**
     * Returns default widget display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return static::DISPLAY_MODE_ICONS;
    }

    /**
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-subcategories block-brands';
    }

    /**
     * @return int
     */
    protected function getIconWidth()
    {
        return $this->getParam(static::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * @return int
     */
    protected function getIconHeight()
    {
        return $this->getParam(static::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ShopByBrand/brands_dialog/';
    }

    /**
     * @param \QSL\ShopByBrand\Model\Image\Brand\Image $image Image
     *
     * @return string
     */
    protected function getAlt($image)
    {
        return $image
            ? $image->getAlt() ?: $image->getBrand()->getName()
            : '';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CATEGORY_ID => $this->getCategoryIdParam(),

            static::PARAM_ORDER           => new \XLite\Model\WidgetParam\TypeString(
                'Order',
                Config::getInstance()->QSL->ShopByBrand->shop_by_brand_dialog_order
            ),
            static::PARAM_LIMIT           => new \XLite\Model\WidgetParam\TypeInt(
                'The maximum number of brands to be displayed',
                Config::getInstance()->QSL->ShopByBrand->shop_by_brand_dialog_limit
            ),
            static::PARAM_DISPLAY_MODE    => new \XLite\Model\WidgetParam\TypeSet(
                'Display mode',
                $this->getDisplayMode(),
                true,
                $this->displayModes
            ),
            static::PARAM_ICON_MAX_WIDTH  => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon width',
                \XLite::getController()->getDefaultMaxImageSize(
                    true,
                    \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                    'Default'
                ),
                true
            ),
            static::PARAM_ICON_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon height',
                \XLite::getController()->getDefaultMaxImageSize(
                    false,
                    \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                    'Default'
                ),
                true
            ),
        ];
    }

    /**
     * @return bool
     */
    protected function isVisibleOnThePage()
    {
        if ($this->getTarget() === 'level_front_page') {
            return Config::getInstance()->QSL->ShopByBrand->shop_by_brand_dialog_lfp;
        }

        return $this->isBrandsBlockOnHomePage()
            ? Config::getInstance()->QSL->ShopByBrand->shop_by_brand_dialog_home
            : Config::getInstance()->QSL->ShopByBrand->shop_by_brand_dialog;
    }

    /**
     * @return string
     */
    protected function getHead()
    {
        return $this->getTarget() === 'main'
            ? static::t('Shop By Brand')
            : static::t('Brands');
    }
}
