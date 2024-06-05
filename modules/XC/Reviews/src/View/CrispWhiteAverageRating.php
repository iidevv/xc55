<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View;

use XCart\Extender\Mapping\Extender;

/**
 * Average product rating widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\CrispWhiteSkin")
 */
class CrispWhiteAverageRating extends \XC\Reviews\View\AverageRating
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/Reviews/average_rating/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
