<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use XLite\Core\Database;

/**
 * Saved credit cards 
 *
 */
class SavedCards extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Saved credit cards');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Saved credit cards');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }

    /**
     * Is zero-auth (card setup) allowed
     *
     * @return bool
     */
    public function allowZeroAuth()
    {
        return ZeroAuth::getInstance()->allowZeroAuth();
    }

    /**
     * Template for the Remove button (or something instead of it)
     *
     * @param int $cardId Card ID
     *
     * @return string
     */
    public function getRemoveTemplate($cardId)
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/account/saved_cards.table.remove.twig';
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
     * Get list of addresses
     *
     * @return bool
     */
    public function isSingleAddress()
    {
        return ZeroAuth::getInstance()->isSingleAddress($this->getProfile());
    }

    /**
     * Get string line for the single address
     *
     * @return string
     */
    public function getSingleAddress()
    {
        return ZeroAuth::getInstance()->getSingleAddress($this->getProfile());
    }

    /**
     * Update default credit card 
     *
     * @return void
     */
    protected function doActionUpdateDefaultCard()
    {
        $profile = $this->getProfile();

        $cardId = \XLite\Core\Request::getInstance()->default_card_id;    

        $addresses = \XLite\Core\Request::getInstance()->address_id;

        if (
            $profile
            && $profile->isCardIdValid($cardId)
            && \XLite\Core\Auth::getInstance()->isLogged()
        ) {
            $this->getProfile()->setDefaultCardId($cardId);

            if ($addresses && is_array($addresses)) {

                // Get list of Address IDs associated with profile
                $profileAddressIds = array_keys(
                    ZeroAuth::getInstance()->getAddressList($profile)
                );

                foreach ($addresses as $cardId => $addressId) {

                    // Validate Address ID and card ID
                    if (
                        in_array($addressId, $profileAddressIds)
                        && $profile->isCardIdValid($cardId)
                    ) {
                        $address = Database::getRepo('\XLite\Model\Address')->find($addressId);
                        $card = Database::getRepo(XpcTransactionData::class)->find($cardId);

                        $card->setBillingAddress($address);
                    }
                }
            }

            Database::getEM()->flush();
        }    
    }

    /**
     * Remove credit card
     *
     * @return void
     */
    protected function doActionRemove()
    {
        $profile = $this->getProfile();

        $cardId = (int)\XLite\Core\Request::getInstance()->card_id;

        $defaultCardId = (int)\XLite\Core\Request::getInstance()->default_card_id;

        if (
            $profile
            && $profile->isCardIdValid($cardId)
            && \XLite\Core\Auth::getInstance()->isLogged()
        ) {
            $this->getProfile()->denyRecharge($cardId);
            if ($cardId === $defaultCardId) {
                $profileSavedCards = $profile->getSavedCards();
                if (!empty($profileSavedCards)) {
                    $profile->setDefaultCardId($profileSavedCards[0]['card_id']);
                } else {
                    $profile->setDefaultCardId(0);
                }
            }
            Database::getEM()->flush();
        }

        $this->setHardRedirect();
        $this->setReturnURL($this->buildURL('saved_cards'));
        $this->doRedirect();
    }

    /**
     * View customer's saved cards and set default card id as 0 if customer has no cards
     *
     * @return void
     */
    protected function doNoAction()
    {
        $profile = $this->getProfile();
        $cards = $profile->getSavedCards();

        if (empty($cards)) {
            $profile->setDefaultCardId(0);
            Database::getEM()->flush();
        }
    }
}
