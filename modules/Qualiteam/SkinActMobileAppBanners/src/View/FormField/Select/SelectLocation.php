<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View\FormField\Select;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("Qualiteam\SkinActSkin")
 */
class SelectLocation extends \QSL\Banner\View\FormField\Select\SelectLocation
{

    protected function getOptions()
    {
        $options = parent::getOptions();
        $banner = $this->getBanner();
        if ($banner && $banner->getForMobileOnly()) {
            unset(
                $options['MainColumn'],
                $options['SecondaryColumn'],
                $options['StandardDouble'],
                $options['WideBottom'],
                $options['WideTop'],
            );

            $optionsSorted = [];
            $optionsSorted['StandardTop'] = $options['StandardTop'];
            $optionsSorted['StandardMiddle'] = $options['StandardMiddle'];
            $optionsSorted['StandardBottom'] = $options['StandardBottom'];
            $options = $optionsSorted;
        }
        return $options;
    }
}