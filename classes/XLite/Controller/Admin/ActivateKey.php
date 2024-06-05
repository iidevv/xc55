<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class ActivateKey extends \XLite\Controller\Admin\ModuleKey
{
    public function getTitle(): string
    {
        return static::t('License key registration');
    }
}
