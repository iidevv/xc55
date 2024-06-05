<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Mailer extends \XLite\View\Mailer
{
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/Reviews/vote_bar.less',
        ]);
    }
}
