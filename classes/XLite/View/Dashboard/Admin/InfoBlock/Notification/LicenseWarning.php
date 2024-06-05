<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Core\URLManager;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\View\LicenseManager\KeysNotice;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="500", zone="admin")
 */
class LicenseWarning extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    use ExecuteCachedTrait;

    protected function getNotificationType(): string
    {
        return 'licenseWarning';
    }

    protected function getClass(): string
    {
        return parent::getClass() . ' license-warning hide';
    }

    protected function getDefaultTemplate(): string
    {
        return 'dashboard/info_block/notification/license_warning.twig';
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'license_manager/common/disallowed_modules_ajax_loader.js';
        $list[] = 'dashboard/info_block/notification/license_warning.js';

        return $list;
    }

    protected function getHeader(): string
    {
        return static::t('License warnings');
    }

    protected function getURLParams(): array
    {
        return [
            'url_params' => [
                'target'    => 'keys_notice',
                'widget'    => KeysNotice::class,
                'returnUrl' => URLManager::getCurrentURL(),
            ],
        ];
    }

    protected function checkACL(): bool
    {
        return parent::checkACL()
            && Auth::getInstance()->hasRootAccess();
    }
}
