<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\Core\Session;

/**
 * Add new credit card
 *
 */
class AddNewCard extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return true;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Add new credit card');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return true;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && ZeroAuth::getInstance()->getPaymentMethod()
            && (
                Auth::getInstance()->isLogged()
                || 'check_cart' == Request::getInstance()->action
                || 'callback' == Request::getInstance()->action
            );
    }

    /**
     * Handles the request.
     *
     * @return void
     */
    public function handleRequest()
    {
        $txn = ZeroAuth::getInstance()->detectTransaction();

        if ($txn) {
            $sessionId = $txn->getXpcDataCell('xpc_session_id')->getValue();
            if (Session::getInstance()->getID() !== $sessionId) {
                Session::getInstance()->loadBySid($sessionId);
            }
            $txn->setXpcDataCell('xpc_session_id', '');
        }

        parent::handleRequest();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Add new credit card');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode('My account');
        $this->addLocationNode('Saved credit cards');
    }

    /**
     * Payment amount for zero-auth (card-setup)
     *
     * @return bool
     */
    public function getAmount()
    {
        return Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_zero_auth_amount;
    }

    /**
     * Payment description for zero-auth (card-setup)
     *
     * @return bool
     */
    public function getDescription()
    {
        return Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_zero_auth_description;
    }

    /**
     * Get profile ID
     *
     * @return int
     */
    public function getProfileId()
    {
        return $this->getProfile()->getProfileId();
    }

    /**
     * Get list of addresses
     *
     * @return array 
     */
    public function getAddressList()
    {
        return ZeroAuth::getInstance()->getAddressList($this->getProfile());
    }

    /**
     * Get address ID
     *
     * return int
     */
    public function getAddressId()
    {
        return ZeroAuth::getInstance()->getAddressId($this->getProfile());
    }

    /**
     * Update address (set selected address for the current zero auth)
     *
     * return void 
     */
    protected function doActionUpdateAddress()
    {
        ZeroAuth::getInstance()->doActionUpdateAddress($this->getProfile());
    }

    /**
     * Show iframe redirect form
     *
     * @return void
     */
    protected function doActionXpcIframe()
    {
        if ($this->getAddressList()) {

            $this->setReturnURL($this->buildURL('add_new_card'));

            ZeroAuth::getInstance()->doActionXpcIframe(
                $this->getProfile(),
                \XLite::getCustomerScript()
            );
        }

    }
    
    /**
     * Customer return action 
     *
     * @return void
     */
    protected function doActionReturn()
    {
        ZeroAuth::getInstance()->doActionReturn();
    }

}
