<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View;

use Qualiteam\SkinActMain\AModule;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = AModule::getModulePath() . 'modules/XC/CrispWhiteSkin/recover_password/style.less';

        return $list;
    }
}