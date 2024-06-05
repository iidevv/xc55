<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use \XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * X-Payments Cloud connector payment method controller
 *
 * @Extender\Mixin
 */
abstract class PaymentMethod extends \XLite\Controller\Admin\PaymentMethod
{
    /**
     * Checks if just_added flag is set
     *
     * @return bool
     */
    public function getXpaymentsJustAdded()
    {
        return (bool)\XLite\Core\Request::getInstance()->just_added;
    }

    /**
     * Register shop URL for empty account
     *
     * @return void
     */
    protected function doNoAction()
    {
        // this should be in run

        parent::doNoAction();

        $account = $this->getPaymentMethod()->getSetting('account');
        $checkAccount = (empty($account) || 'localhost' == $account);

        if (
            $this->isXpaymentsOperatedMethod()
            && $checkAccount
            && \Includes\Utils\ConfigParser::getOptions(array('service', 'is_cloud'))
        ) {
            XPaymentsHelper::registerCloudShopUrl();
        }
    }

    /**
     * Returns X-Payments Cloud main payment method instance
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getXpaymentsPaymentMethod()
    {
        return XPaymentsHelper::getPaymentMethod();
    }

    /**
     * Checks if current payment method is X-Payments Cloud or Apple Pay
     *
     * @return bool
     */
    public function isXpaymentsOperatedMethod()
    {
        return $this->getPaymentMethod()->isXpayments();
    }

    /**
     * Save connect settings
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        if ($this->isXpaymentsOperatedMethod()) {

            $wasConfigured = $this->getPaymentMethod()->isConfigured();
            if (!$wasConfigured) {
                // Set fake flag to trigger auto-enable of XP Cloud when it is configured inside parent method
                \XLite\Core\Request::getInstance()->just_added = true;
            }

            $connectionKey = \XLite\Core\Request::getInstance()->connection_key;

            if (!empty($connectionKey)) {

                try {

                    $settings = base64_decode($connectionKey);

                    if (defined('JSON_THROW_ON_ERROR')) {
                        $settings = json_decode($settings, true, JSON_THROW_ON_ERROR);
                    } else {
                        // PHP 7.2 compatibility
                        $settings = json_decode($settings, true);
                    }

                    if (!empty($settings) && is_array($settings)) {
                        \XLite\Core\Request::getInstance()->settings = $settings;
                    }

                } catch (\Exception $exception) {

                    \XLite\Core\TopMessage::addWarning('Invalid connection key');
                }
            }
        }

        parent::doActionUpdate();

        if ($this->isXpaymentsOperatedMethod()) {

            if ($this->isAJAX()) {
                // Actually here will be only main XP Cloud method because Apple Pay submits settings to main method only
                $this->setSilenceClose(true);
                \XLite\Core\TopMessage::getInstance()->clearAJAX();
            }

            $updatedMethod = $this->getPaymentMethod();

            if ($wasConfigured != $updatedMethod->isConfigured()) {
                if (!$wasConfigured && $updatedMethod->isEnabled()) {
                    // Automatically enable wallet methods if main method was configured and enabled
                    foreach (XPaymentsHelper::getWalletMethods() as $wallet) {
                        $wallet->setEnabled(true);
                    }
                    \XLite\Core\Database::getEM()->flush();
                }
                \XLite\Core\Event::xpaymentsReloadPaymentStatus();
            }
        }
    }
}
