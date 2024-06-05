<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Button;

use XLite\View\Button\Link;

/**
 * Link as button specific for "Careers" page
 */
class JobButton extends Link
{
    protected function getDefaultButtonType()
    {
        return '';
    }
}
