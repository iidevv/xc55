<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Alert;

abstract class Warning extends \XLite\View\Alert
{
    protected function getClass()
    {
        return parent::getClass() . ' alert-warning';
    }
}
