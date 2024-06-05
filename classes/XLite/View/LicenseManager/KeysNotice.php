<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LicenseManager;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight=0)
 */
class KeysNotice extends \XLite\View\LicenseManager\ALicenseManager
{
    protected ?string $purchaseAllURL = null;

    public static function getAllowedTargets(): array
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'keys_notice';

        return $list;
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'license_manager/keys_notice/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'license_manager/keys_notice/controller.js';

        return $list;
    }

    protected function getDefaultTemplate(): string
    {
        return 'license_manager/keys_notice/body.twig';
    }

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite\Core\Marketplace::getBusinessPurchaseURL();
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
     * Get URL for 'Remove unallowed modules' action
     *
     * @return string
     */
    protected function getRemoveModulesURL()
    {
        return \XLite::getInstance()->getShopURL('service.php?/removeUnallowedModules');
    }

    /**
     * Get URL for 'Back to Trial mode' action
     *
     * @return string
     */
    protected function getBackToTrialURL()
    {
        return $this->buildURL('module_key', 'unset_core_license');
    }

    /**
     * Get URL for 'Back to Trial mode' action
     *
     * @return string
     */
    protected function getRecheckURL()
    {
        return $this->buildURL(
            'keys_notice',
            'recheck',
            [
                'returnUrl' => \XLite\Core\Request::getInstance()->returnUrl
            ]
        );
    }

    /**
     * Return true if fraud status has been confirmed
     *
     * @return boolean
     */
    protected function isFraudStatusConfirmed()
    {
        $result = false;

        if (\XLite\Core\Marketplace::getInstance()->isFraud()) {
            $result = true;
            \XLite\Core\Session::getInstance()->fraudWarningDisplayed = true;
            \XLite\Core\Session::getInstance()->shouldDisableUnallowedModules = true;
        }

        return $result;
    }

    /**
     * Get 'Purchase all' button URL
     *
     * @return string
     */
    protected function getPurchaseAllURL()
    {
        if (!isset($this->purchaseAllURL)) {
            $urlParamsAggregated = [
                'action' => 'add_items'
            ];

            $this->purchaseAllURL = $urlParamsAggregated
                ? \XLite\Core\Marketplace::getPurchaseURL(null, $urlParamsAggregated, true)
                : '';
        }

        return $this->purchaseAllURL;
    }

    /**
     * Return true if 'Purchase all' button should be displayed
     *
     * @return boolean
     */
    protected function isDisplayPurchaseAllButton()
    {
        return (bool)$this->getPurchaseAllURL();
    }
}
