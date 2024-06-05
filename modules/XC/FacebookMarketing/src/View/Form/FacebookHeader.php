<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\Form;

class FacebookHeader extends \XLite\View\Form\Settings
{
    /**
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'facebook_marketing';
    }

    /**
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'set_pixel_key';
    }
}
