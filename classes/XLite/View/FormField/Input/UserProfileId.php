<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * \XLite\View\FormField\Input\UserProfileId
 */
class UserProfileId extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_RADIO;
    }
}
