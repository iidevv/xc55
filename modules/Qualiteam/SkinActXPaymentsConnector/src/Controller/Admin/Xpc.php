<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Admin;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;
use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XLite\Controller\Admin\Module;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\Model\Payment\Method;

/**
 * X-Payments Connector module settings
 *
 */
class Xpc extends Module
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Instance of settings 
     *
     * @var Settings
     */
    protected $settings = null;

    /**
     * Initialize settings 
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function init()
    {
        parent::init();

        $this->settings = Settings::getInstance();
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        if (!$this->settings->isPageValid(Request::getInstance()->page)) {

            $this->setHardRedirect();

            $this->setReturnURL(
                $this->buildURL(
                    'xpc',
                    '',
                    array(
                        'page'  => $this->settings->getDefaultPage(),
                    )
                )
            );

            $this->doRedirect();
        }
    }

    /**
     * Get current module ID
     *
     * @return integer
     */   
    public function getModuleID()
    {
        return \Includes\Utils\Module\Module::buildId('Qualiteam', 'SkinActXPaymentsConnector');
    }

    /**
     * Check if connection to X-Payments is OK 
     *
     * @return boolean
     */
    public function isConnected()
    {
        $settings = $this->settings;

        return $settings::RESULT_FAILED !== $settings->testConnection();
    }

    /**
     * Wrapper for X-Payments client isModuleConfigured() method 
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return XPaymentsClient::getInstance()->isModuleConfigured();
    }

    /**
     * Check - is there are any actve payment methods which can save cards 
     *
     * @return boolean
     */
    public function hasActiveMethodsSavingCards()
    {
        $paymentMethods = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findAllActive();

        $result = false;

        foreach ($paymentMethods as $pm) {
            if (
                XPayments::class == $pm->getClass()
                && 'Y' == $pm->getSetting('saveCards')
            ) {
                $result = true;
                break;
            }
        }


        return $result;
    }

    /**
     * Check - is payment configurations imported early or not
     *
     * @return boolean
     */
    public function hasPaymentMethods()
    {
        return $this->settings->hasPaymentMethods();
    }

    /**
     * Get payment methods
     *
     * @return array
     */
    public function getPaymentMethods($processor = 'XPayments')
    {
        return $this->settings->getPaymentMethods($processor);
    }

    /**
     * Update payment methods: save cards, currency, etc 
     *
     * @return void
     */
    protected function doActionUpdatePaymentMethods()
    {
        $methods = $this->getPaymentMethods();

        $request = Request::getInstance()->data;

        $config = Config::getInstance()->Qualiteam->SkinActXPaymentsConnector;

        $saveCardsMethodSubmitted = false;

        foreach ($methods as $method) {

            $pmData = Request::getInstance()->data[$method->getMethodId()];

            if (
                isset($pmData['save_cards'])
                && 'Y' == $pmData['save_cards']
                && 'Y' == $method->getSetting('canSaveCards')
            ) {
                $method->setSetting('saveCards', 'Y');

                if (
                    !empty($pmData['enabled'])
                    && !$saveCardsMethodSubmitted
                ) {
                    // The second condition is not necesary.
                    // But in this case noone will ask,
                    // why the last method is set for zero auth instead of first
                    $saveCardsMethodSubmitted = $method;
                }

            } else {
                $method->setSetting('saveCards', 'N');
            }

            if (
                isset($pmData['currency'])
                && $pmData['currency']
            ) {
                $method->setSetting('currency', $pmData['currency']);
            }

            if (
                isset($pmData['enabled'])
                && $pmData['enabled']
            ) {
                $method->setEnabled(true);

            } else {
                $method->setEnabled(false);
            }
        
        }

        $saveCardsMethodInStore = $this->getPaymentMethods('SavedCard');

        if ($saveCardsMethodSubmitted) {

            $savedCardPM = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => 'SavedCard']);

            if ($savedCardPM) {
                // Make Saved credit card payment method the real one if at least one of X-Payments payment methods saves cards 
                $savedCardPM->setFromMarketplace(false);
                $savedCardPM->setAdded(true);
                $savedCardPM->setEnabled(true);

            } else {
                // Add Saved credit card payment method if no one Saved Card PM is stored in the DB
                $pm = new Method;
                \XLite\Core\Database::getEM()->persist($pm);
                $pm->setClass(SavedCard::class);
                $pm->setServiceName('SavedCard');
                $pm->setName('Use a saved credit card');
                $pm->setType(Method::TYPE_CC_GATEWAY);
                $pm->setAdded(true);
                $pm->setEnabled(true);
            }

        } elseif (
            $saveCardsMethodInStore
            && !$saveCardsMethodSubmitted
        ) {
            // Make Saved credit card payment method the fake one if all X-Payments payment methods do not save cards
            foreach ($saveCardsMethodInStore as $pm) {
                $pm->setAdded(false);
                $pm->setEnabled(false);
                $pm->setFromMarketplace(true);
            }
        }

        // Configure the Zero Auth if it's not done yet
        if (
            $saveCardsMethodSubmitted
            && !ZeroAuth::getInstance()->allowZeroAuth()
            && !ZeroAuth::DISABLED == $config->xpc_zero_auth_method_id
        ) {
            $settings = array(
                'xpc_zero_auth_method_id' => $saveCardsMethodSubmitted->getMethodId(),
            );

            if (!$config->xpc_zero_auth_amount) {
                $settings['xpc_zero_auth_amount'] = '1.00';
            }
            if (!$config->xpc_zero_auth_description) {
                $settings['xpc_zero_auth_description'] = ZeroAuth::getDefaultDescription();
            }

            foreach ($settings as $key => $value) {

                $setting = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
                    array(
                       'name' => $key,
                       'category' => 'Qualiteam\SkinActXPaymentsConnector'
                    )
                );

                \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                    $setting,
                    array('value' => $value)
                );
            }
        }

        \XLite\Core\Database::getEM()->flush();

        Config::updateInstance();
    }

    /**
     * Deploy configuration
     *
     * @return void
     */
    protected function doActionDeployConfiguration()
    {
        $errorMsg = $this->settings->deployConfiguration(Request::getInstance()->deploy_configuration);

        if ($errorMsg) {
            \XLite\Core\TopMessage::addError($errorMsg);

        } else {
            \XLite\Core\TopMessage::addInfo('Configuration has been successfully deployed');

            $this->setHardRedirect();

            $this->setReturnURL(
                $this->buildURL(
                    'xpc',
                    '',
                    array(
                        'page'  => $this->settings->getPage('PAGE_PAYMENT_METHODS'),
                    )
                )
            );

            $this->doRedirect();

        }
    }

    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        Config::updateInstance();

        $settings = $this->settings;

        $connectResult = $settings->testConnection(false);

        if ($settings::RESULT_FAILED !== $connectResult) {
            $settings->importPaymentMethods($connectResult);
        }

        $page = $settings->isPageValid(Request::getInstance()->page)
            ? Request::getInstance()->page
            : $settings->getDefaultPage();

        $this->setReturnURL(
            $this->buildURL(
                'xpc',
                null,
                array('page' => $page)
            )
        );
    }

    /**
     * Request and import payment configurations
     *
     * @return void
     */
    protected function doActionImport()
    {
        $settings = $this->settings;

        $connectResult = $settings->testConnection(false);

        if ($settings::RESULT_FAILED !== $connectResult) {
            $settings->importPaymentMethods($connectResult);
        }
    }

    /**
     * Get link to XPayments admin panel
     *
     * @return boolean
     */
    public function getXPAdminLink()
    {
        $xp_url = trim(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_xpayments_url, '/');

        return $xp_url . '/admin.php?target=payment_confs';
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return \Qualiteam\SkinActXPaymentsConnector\View\Model\Settings::class;
    }

    /**
     * Get pages pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        $list += $this->settings->getAllPages();

        if ($this->isConfigured()) {
        
            unset($list[$this->settings->getPage('PAGE_WELCOME')]);
        }

        return $list;
    }

    

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        foreach ($this->settings->getAllPages() as $page => $title) {
            $list[$page] = 'modules/Qualiteam/SkinActXPaymentsConnector/settings/settings.twig';
        }    

        if ($this->isConfigured()) {

            unset($list[$this->settings->getPage('PAGE_WELCOME')]);
        }

        return $list;
    }

}
