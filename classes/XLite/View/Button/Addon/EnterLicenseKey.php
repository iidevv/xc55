<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

use XCart\Extender\Mapping\ListChild;

/**
 * Enter license key popup text
 *
 * @ListChild (list="marketplace.addons-filters", weight="300", zone="admin")
 */
class EnterLicenseKey extends \XLite\View\Button\APopupButton
{
    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/enter_license_key.js';

        return $list;
    }

    protected function getDefaultLabel(): string
    {
        return 'Activate license key';
    }

    protected function prepareURLParams(): array
    {
        return [
            'target' => 'module_key',
            'action' => 'view',
            'widget' => '\XLite\View\LicenseManager\AddonKey',
        ];
    }

    protected function getClass(): string
    {
        return parent::getClass() . ' enter-license-key';
    }
}
