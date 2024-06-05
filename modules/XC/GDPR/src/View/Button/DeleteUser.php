<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class DeleteUser extends \XLite\View\Button\DeleteUser
{
    protected function getDefaultLabel()
    {
        return static::t('Delete my personal data and account');
    }
}
