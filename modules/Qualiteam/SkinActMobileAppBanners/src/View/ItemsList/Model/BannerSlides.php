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
class BannerSlides extends \QSL\Banner\View\ItemsList\Model\BannerSlides
{

    protected function defineColumns()
    {
        $cols = parent::defineColumns();

        if ($this->getBanner() && $this->getBanner()->getForMobileOnly()) {

            $cols['appPosition'] = [
                static::COLUMN_NAME => static::t('SkinActMobileAppBanners appPosition'),
                static::COLUMN_CLASS => '\Qualiteam\SkinActMobileAppBanners\View\FormField\Inline\AppPosition',
                static::COLUMN_ORDERBY => 210,
                static::COLUMN_HEAD_HELP => static::t('SkinActMobileAppBanners appPosition help'),
            ];

            $preserve = [
                'image',
                'link',
                'position'
            ];

            foreach ($cols as $name => $data) {
                if (!in_array($name, $preserve, true)) {
                    unset($cols[$name]);
                }
            }

        }

        if ($this->getBanner() && !$this->getBanner()->getForMobileOnly()) {
            unset($cols['mobile_position']);
        }

        $cols['position'][static::COLUMN_NAME] = \XLite\Core\Translation::lbl('SkinActMobileAppBanners position');

        return $cols;
    }

}