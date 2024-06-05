<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * StatesForCountries
 */
class StatesForCountries extends \XLite\View\FormField\Input\Checkbox\YesNo
{
    public const SESSION_CELL_NAME = 'states_for_selected_countries';

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return (bool)\XLite\Core\Session::getInstance()->{static::SESSION_CELL_NAME};
    }
}
