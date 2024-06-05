<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LicenseManager;

use XCart\Extender\Mapping\ListChild;

/**
 * License key activation widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class LicenseKey extends \XLite\View\LicenseManager\ALicenseManager
{
    public static function getAllowedTargets(): array
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'activate_key';

        return $result;
    }

    protected function getDefaultTemplate(): string
    {
        return 'license_manager/activate_key/body.twig';
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'license_manager/activate_key/style.less',
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

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \Includes\Utils\URLManager::getAffiliatedXCartURL(
            \XLite::PRODUCER_SITE_URL . 'contact-us.html',
            'activate_key',
            null,
            'en'
        );
    }

    /**
     * URL of the X-Cart company's Contact Us page
     *
     * @return string
     */
    protected function getContactUsURL()
    {
        return \XLite\Core\Marketplace::getContactUsURL();
    }

    /**
     * Check if module activation
     *
     * @return boolean
     */
    protected function isModuleActivation()
    {
        return (bool) \XLite\Core\Request::getInstance()->isModule;
    }
}
