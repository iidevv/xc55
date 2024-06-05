<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class GMVTracker extends \XLite\View\AView
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'gmv_tracker/script.js';

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return null;
    }

    public static function getDisallowedTargets()
    {
        return ['login'];
    }
}
