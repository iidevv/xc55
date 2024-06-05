<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Export;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Begin extends \XLite\View\Export\Begin
{
    protected function getSections()
    {
        $return = parent::getSections();

        $return['QSL\BackInStock\Logic\Export\Step\RecordsStock'] = 'Back-in-stock records';
        $return['QSL\BackInStock\Logic\Export\Step\RecordsPrice'] = 'Price-drop records';

        return $return;
    }
}
