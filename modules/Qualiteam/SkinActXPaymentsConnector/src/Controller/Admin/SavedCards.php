<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActXPaymentsConnector\Controller\Admin;

use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use XLite\Controller\Admin\AAdmin;
use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * Saved credit cards
 */
class SavedCards extends AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Edit profile');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $profile = (null !== $this->getCustomerProfile())
            ? $this->getCustomerProfile()
            : Auth::getInstance()->getProfile();
        $isAnonymous = $profile->getAnonymous();

        return (
            parent::checkACL()
            || Auth::getInstance()->isPermissionAllowed('manage users')
            )
            && !$isAnonymous;
    }

    /**
     * Get saved cards
     *
     * @return array
     */
    public function getSavedCards()
    {
        return $this->getCustomerProfile()
            ? $this->getCustomerProfile()->getSavedCards()
            : null;
    }

    /**
     * Get customer profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        $profileId = \XLite\Core\Request::getInstance()->profile_id;
        if (empty($profileId)) {
            $profileId = Auth::getInstance()->getProfile()->getProfileId();
        }

        return Database::getRepo('XLite\Model\Profile')
            ->find(intval($profileId));
    }

    /**
     * Get customer profile Id
     *
     * @return integer
     */
    public function getCustomerProfileId()
    {
        return $this->getCustomerProfile()->getProfileId();
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
     * Update default credit card, card addresses and remove cards
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $profile = $this->getCustomerProfile();

        $defaultCardId = (int)\XLite\Core\Request::getInstance()->default_card_id;
        $delete = \XLite\Core\Request::getInstance()->delete;

        $addresses = \XLite\Core\Request::getInstance()->address_id;

        if ($profile) {
            // Mark card as default
            if ($profile->isCardIdValid($defaultCardId)) {
                $profile->setDefaultCardId($defaultCardId);
            }

            // Remove credit card
            // I.e deny recharges for it
            if ($delete && is_array($delete)) {
                foreach ($delete as $cardId => $v) {

                    if ($profile->isCardIdValid($cardId)) {
                        $profile->denyRecharge($cardId);
                    }

                    if ($cardId === $defaultCardId) {
                        $profileSavedCards = $profile->getSavedCards();
                        if (!empty($profileSavedCards)) {
                            $profile->setDefaultCardId($profileSavedCards[0]['card_id']);
                        } else {
                            $profile->setDefaultCardId(0);
                        }
                    }

                }
            }

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
     * View customer's saved cards and set default card id as 0 if customer has no cards
     *
     * @return void
     */
    protected function doNoAction()
    {
        $profile = $this->getCustomerProfile();
        $cards = $profile->getSavedCards();

        if (empty($cards)) {
            $profile->setDefaultCardId(0);
            Database::getEM()->flush();
        }
    }
}
