<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\CustomerSatisfaction\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\CustomerSatisfaction")
 */
class CustomerSurvey extends \QSL\CustomerSatisfaction\View\CustomerSurvey
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return string[]
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [ 'modules/QSL/CustomerSatisfaction/css/style.less' ]
        );
    }
}
