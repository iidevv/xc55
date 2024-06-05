<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Confirm;

/**
 * Confirmation with password widget
 */
class WithPassword extends \XLite\View\AView
{
    /**
     * getDefaultTemplate
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'confirm/with_password.twig';
    }
}
