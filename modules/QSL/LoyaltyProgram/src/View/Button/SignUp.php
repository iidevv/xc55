<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Button;

/**
 * "Sign Up" button.
 */
class SignUp extends \XLite\View\Button\Link
{
    /**
     * Get the button label.
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Sign up on loyalty program';
    }

    /**
     * Define widget parameters.
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_LOCATION]->setValue($this->buildURL('profile', '', ['mode' => 'register']));
    }

    /**
     * Get CSS classes for the button.
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'bright checkout';
    }
}
