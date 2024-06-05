<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LicenseManager;

use XLite\Core\Request;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * @ListChild (list="admin.center", zone="admin", weight=0)
 */
class TrialNotice extends \XLite\View\LicenseManager\ALicenseManager implements ProviderInterface
{
    public static function getAllowedTargets(): array
    {
        return [
            'trial_notice', // the popup window target
            'order',
            'order_list',
            'product_list',
        ];
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'license_manager/trial_notice/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'license_manager/trial_notice/controller.js';
        $list[] = 'license_manager/activate_key/controller.js';

        return $list;
    }

    protected function getDefaultTemplate(): string
    {
        return 'license_manager/trial_notice/body.twig';
    }

    public function getPreloadedLanguageLabels(): array
    {
        return [
            'Your X-Cart trial has expired!'        => static::t('Your X-Cart trial has expired!'),
            'Your X-Cart trial expires in X day(s)' => static::t('Your X-Cart trial expires in X day(s)'),
        ];
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
     * @return string
     */
    protected function getRegisterLicenseURL()
    {
        return \XLite\Core\Converter::buildURL('', '', ['activate_key' => true], \XLite::getAdminScript());
    }

    /**
     * URL of the X-Cart company's License Agreement page
     *
     * @return string
     */
    protected function getLicenseAgreementURL()
    {
        return \XLite\Core\Marketplace::getLicenseAgreementURL();
    }

    /**
     * @return boolean
     */
    protected function isPopup()
    {
        return \XLite::getController()->getTarget() === 'trial_notice';
    }

    public function showOnlyExpired(): bool
    {
        return in_array(Request::getInstance()->target, self::getAllowedTargets()) && !$this->isPopup();
    }
}
