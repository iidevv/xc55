<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View\ItemsList\Model;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("Qualiteam\SkinActBannerAdvanced")
 */
class Banner extends \QSL\Banner\View\ItemsList\Model\Banner
{
    protected function defineColumns()
    {
        $cols = parent::defineColumns();

        unset($cols['mobile_position']);
        $cols['position'][static::COLUMN_ORDERBY] = 100;
        $cols['position'][static::COLUMN_NAME] = \XLite\Core\Translation::lbl('SkinActMobileAppBanners position');
        //$cols['mobile_position'][static::COLUMN_ORDERBY] = 200;
        $cols['title'][static::COLUMN_ORDERBY] = 300;
        $cols['location'][static::COLUMN_ORDERBY] = 500;

        $cols['banner_view'] = [
            static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActMobileAppBanners View of the banner'),
            static::COLUMN_ORDERBY => 400,
        ];

        return $cols;
    }

    protected function getBannerViewColumnValue($entity)
    {
        if ($entity && $entity->getForMobileOnly()) {
            return static::t('SkinActMobileAppBanners for mobile app only');
        }

        return '';
    }

}