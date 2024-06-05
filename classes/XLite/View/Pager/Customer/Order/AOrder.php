<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Customer\Order;

use XLite\Model\WidgetParam\TypeCheckbox;

/**
 * Abstract pager class for the OrdersList widget
 */
abstract class AOrder extends \XLite\View\Pager\Customer\ACustomer
{
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        /** @var TypeCheckbox $paramShowItemsPerPageSelector */
        $paramShowItemsPerPageSelector = $this->widgetParams[static::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR];
        $paramShowItemsPerPageSelector->setValue(false);
    }
}
