<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LicenseManager;

/**
 * Addons search and installation widget
 */
class ALicenseManager extends \XLite\View\Dialog
{
    protected function getModuleId(): int
    {
        return \XLite\Core\Request::getInstance()->moduleId;
    }

    protected function getDir(): string
    {
        return 'modules_manager';
    }
}
