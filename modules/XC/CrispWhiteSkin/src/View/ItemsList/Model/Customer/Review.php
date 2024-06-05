<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\ItemsList\Model\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Review details
 * @Extender\Mixin
 * @Extender\Depend("XC\Reviews")
 */
class Review extends \XC\Reviews\View\ItemsList\Model\Customer\Review
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/Reviews/reviews_page/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
