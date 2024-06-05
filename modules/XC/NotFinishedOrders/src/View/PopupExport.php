<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View;

use XCart\Extender\Mapping\Extender;

/**
 * Popup export
 * @Extender\Mixin
 */
class PopupExport extends \XLite\View\PopupExport
{
    /**
     * Get inner widget class name
     *
     * @return string
     */
    protected function getInnerWidget()
    {
        $result = parent::getInnerWidget();

        if ($this->isExportHasOnlyNFO()) {
            $result = 'XC\NotFinishedOrders\View\Export\OnlyNFOSelected';
        }

        return $result;
    }
}
