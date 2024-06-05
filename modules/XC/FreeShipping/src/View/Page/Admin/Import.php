<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\Page\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Import extends \XLite\View\Page\Admin\Import
{
    protected function getInnerWidget()
    {
        $widget = parent::getInnerWidget();

        if (
            $widget === 'XLite\View\Import\Completed'
            && $this->getImporter()
            && $this->getImporter()->isDisplayFreeShippingUpdateNotice()
        ) {
            $widget = '\XC\FreeShipping\View\Import\FreeShippingUpdateNotification';
        }

        return $widget;
    }
}
