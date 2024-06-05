<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LicenseManager;

use XCart\Extender\Mapping\ListChild;

/**
 * Enter addon key page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AddonKey extends \XLite\View\LicenseManager\ALicenseManager
{
    public static function getAllowedTargets(): array
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'module_key';

        return $result;
    }

    protected function getDefaultTemplate(): string
    {
        return 'license_manager/enter_key/body.twig';
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'license_manager/enter_key/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'license_manager/activate_key/controller.js';

        return $list;
    }
}
