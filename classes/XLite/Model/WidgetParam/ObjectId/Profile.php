<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam\ObjectId;

/**
 * Profiel id Widget parameter
 */
class Profile extends \XLite\Model\WidgetParam\TypeObjectId
{
    /**
     * Return object class name
     *
     * @return string
     */
    protected function getClassName()
    {
        return '\XLite\Model\Profile';
    }
}
