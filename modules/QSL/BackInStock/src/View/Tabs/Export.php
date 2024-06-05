<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Export extends \XLite\View\Tabs\Export
{
    /**
     * @inheritDoc
     */
    protected function defineSections()
    {
        return parent::defineSections() + [
                'QSL\BackInStock\Logic\Export\Step\RecordsStock' => ['label' => 'Back-in-stock records', 'position' => 95],
                'QSL\BackInStock\Logic\Export\Step\RecordsPrice' => ['label' => 'Price-drop records', 'position' => 96],
            ];
    }
}
