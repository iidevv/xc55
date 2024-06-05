<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View;

use XCart\Extender\Mapping\Extender;

/**
 * Order item attributes
 * @Extender\Mixin
 */
class OrderItemAttributes extends \XLite\View\OrderItemAttributes
{
    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible();

        if (
            \XLite\Core\Request::getInstance()->target == 'order'
            && (
                \XLite\Core\Request::getInstance()->page == 'create_return'
                || \XLite\Core\Request::getInstance()->page == 'modify_return'
            )
        ) {
            $result = false;
        }

        return $result;
    }
}
