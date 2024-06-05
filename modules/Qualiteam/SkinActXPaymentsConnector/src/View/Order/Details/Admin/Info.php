<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Order\Details\Admin;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActXPaymentsConnector\Core\Kount;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\FraudCheckData;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * Order info
 *
 * @Extender\Mixin
 */
class Info extends \XLite\View\Order\Details\Admin\Info
{
    /**
     * Get skin directory
     *
     * @return string
     */
    protected static function getDirectory()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/order';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = self::getDirectory() . '/style.css';
        $list[] = self::getDirectory() . '/add_info/style.css';
        $list[] = self::getDirectory() . '/saved_cards/style.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list['css'][] = 'modules/Qualiteam/SkinActXPaymentsConnector/cc_type_sprites.css';
        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = self::getDirectory() . '/script.js';

        return $list;
    }

    /**
     * Javascript code for recharge popup 
     *
     * @return string 
     */
    protected function getRechargeJsCode()
    {
        $orderNumber = '\'' . $this->getOrder()->getOrderNumber() . '\'';
        $amount = '\'' . $this->getOrder()->getAomTotalDifference() . '\'';

        $code = 'showRechargeBox(' . $orderNumber . ', ' . $amount . ');';

        return $code;
    }

    /**
     * Is recharge allowed for the order
     *
     * @return boolean
     */
    protected function isAllowRecharge()
    {
        return $this->getOrder()->isAllowRecharge();
    }

    /**
     * Get Kount data
     *
     * @return object
     */
    protected function getKountData()
    {
        return Kount::getInstance()->getKountData($this->getOrder());
    }

    /**
     * Get list of Kount errors
     *
     * @return array
     */
    protected function getKountErrors()
    {
        return Kount::getInstance()->getKountErrors($this->getOrder());
    }

    /**
     * Get list of Kount triggered rules
     *
     * @return array
     */
    protected function getKountRules()
    {
        return Kount::getInstance()->getKountRules($this->getOrder());
    }

    /**
     * Get Kount result as text
     *
     * @return string
     */
    protected function getKountMessage()
    {
        return Kount::getInstance()->getKountMessage($this->getOrder());
    }

    /**
     * Get Kount transaction ID
     *
     * @return string
     */
    protected function getKountTransactionId()
    {
        return Kount::getInstance()->getKountTransactionId($this->getOrder());
    }

    /**
     * Get Kount score
     *
     * @return string
     */
    protected function getKountScore()
    {
        return Kount::getInstance()->getKountScore($this->getOrder());
    }

    /**
     * Get CSS class for Kount score
     *
     * @return string
     */
    protected function getKountScoreClass()
    {
        return Kount::getInstance()->getKountScoreClass($this->getOrder());
    }

    /**
     * Check - display AntiFraud module advertisment or not
     *
     * @return boolean
     */
    protected function isDisplayAntiFraudAd()
    {
        $result = parent::isDisplayAntiFraudAd();

        if ($result) {

            if (
                Kount::getInstance()->getKountData($this->getOrder())
                || (
                    Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
                    && Auth::getInstance()->isVendor()
                )
            ) {

                $result = false;

            } elseif ($this->getOrder()->getFraudCheckData()) {

                foreach ($this->getOrder()->getFraudCheckData() as $fraudCheckData) {
                    if (
                        FraudCheckData::CODE_KOUNT == $fraudCheckData->getCode()
                        || FraudCheckData::CODE_NOFRAUD == $fraudCheckData->getCode()
                        || FraudCheckData::CODE_ANTIFRAUD == $fraudCheckData->getCode()
                    ) {
                        $result = false;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
