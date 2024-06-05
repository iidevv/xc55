<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Button\Admin\ModifyReturn;

/**
 * Modify return methods
 */
class Decline extends \XLite\View\Button\Regular
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'decline_return';
    }
}
