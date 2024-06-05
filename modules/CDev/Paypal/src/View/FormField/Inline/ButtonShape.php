<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Inline;

class ButtonShape extends AButton
{
    /**
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'CDev\Paypal\View\FormField\Select\ButtonShape';
    }
}
