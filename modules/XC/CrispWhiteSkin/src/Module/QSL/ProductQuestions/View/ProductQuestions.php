<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\ProductQuestions\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ProductQuestions")
 */
class ProductQuestions extends \QSL\ProductQuestions\View\ProductQuestions
{
    /**
     * @return string[]
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'file'  => 'modules/QSL/ProductQuestions/css/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less'
            ]
        );
    }
}
