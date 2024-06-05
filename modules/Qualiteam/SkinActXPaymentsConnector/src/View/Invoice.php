<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View;

use Qualiteam\SkinActXPaymentsConnector\Core\Kount;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Mailer;

/**
 * Invoice page
 *
 * @Extender\Mixin
 */
class Invoice extends \XLite\View\Invoice
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/invoice/style.css';

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
     * Display Kount result on the invoice or not
     *
     * @return bool 
     */
    protected function isDisplayKountResult()
    {
        return Kount::getInstance()->getKountData($this->getOrder())
            && Mailer::getInstance()->isMailSendToAdmin();
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
     * Get Kount error CSS style
     *
     * @return string
     */
    protected function getKountErrorStyle()
    {
        return 'padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px;background-color: #f2dede; border-color: #ebccd1; color: #a94442;';
    }
}
