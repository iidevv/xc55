<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LicenseManager;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Model\Role\Permission;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * @ListChild (list="admin.main.page.before_header", weight="500", zone="admin")
 */
class CoreLicense extends \XLite\View\AView implements ProviderInterface
{
    protected function getDefaultTemplate(): string
    {
        return 'license_manager/header/body.twig';
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'license_manager/common/core_license_ajax_loader.js';
        if (!$this->isDemoMode()) {
            $list[] = 'license_manager/common/marketplace_links.js';
        }
        $list[] = 'license_manager/header/controller.js';
        $list[] = 'rebuild/script.js';
        $list[] = 'license_manager/common/activate_key_handler.js';

        if ($this->isAutoDisplayAllowed()) {
            $list[] = 'license_manager/common/disallowed_enabled_modules_ajax_loader.js';
            $list[] = 'license_manager/common/auto_display.js';
        }

        return $list;
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'license_manager/header/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    public function getPreloadedLanguageLabels(): array
    {
        return [
            'Your X-Cart trial expires in X day(s)' => static::t('Your X-Cart trial expires in X day(s)'),
            'Trial has expired!' => static::t('Trial has expired!'),
            'The "{{name}}" module version is incompatible with your core version and cannot be enabled.' => static::t('The "{{name}}" module version is incompatible with your core version and cannot be enabled.'),
            'X-Cart license key has been successfully verified' => static::t('X-Cart license key has been successfully verified'),
            'License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author.' => static::t('License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author.'),
        ];
    }

    protected function isAutoDisplayAllowed(): bool
    {
        return !Request::getInstance()->activate_key
            && \XLite::getController()->getTarget() !== 'apps';
    }

    protected function checkACL(): bool
    {
        return parent::checkACL()
            && Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS);
    }

    protected function isVisible(): bool
    {
        return parent::isVisible()
            && Auth::getInstance()->isAdmin();
    }

    /**
     * Is used to preserve adding xc5_shop_identifier to links to market.x-cart.com
     */
    protected function isDemoMode(): bool
    {
        return (bool) \Includes\Utils\ConfigParser::getOptions(['demo', 'demo_mode']) ?? false;
    }
}
