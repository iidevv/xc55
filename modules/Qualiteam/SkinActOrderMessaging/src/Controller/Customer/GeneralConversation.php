<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Controller\Customer;


use XC\VendorMessages\Model\Conversation;
use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Core\Database;

class GeneralConversation extends \XLite\Controller\Customer\ACustomer
{

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && Auth::getInstance()->isLogged();
    }

    /**
     * @inheritdoc
     */
    protected function getLocation()
    {
        return static::t('Conversation');
    }

    /**
     * @inheritdoc
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Messages'), $this->buildURL('messages'));
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActOrderMessaging Conversation');
    }

    /**
     * Return current conversation
     *
     * @return \XC\VendorMessages\Model\Conversation
     */
    public function getConversation()
    {
        $profile = Auth::getInstance()->getProfile();

        $conversation = Database::getRepo('XC\VendorMessages\Model\Conversation')->findGeneralDialogue(
            $profile->getProfileId(),
        );

        if (!$conversation) {
            $conversation = $this->createGeneralConversation();
        }

        return $conversation;
    }

    /**
     * Return current conversation Id
     *
     * @return integer
     */
    public function getConversationId()
    {
        return $this->getConversation() ? $this->getConversation()->getId() : null;
    }

    /**
     * Create new general conversation
     *
     * @return Conversation
     */
    protected function createGeneralConversation()
    {
        $profile = Auth::getInstance()->getProfile();

        $conversation = Database::getRepo('\XC\VendorMessages\Model\Conversation')
            ->createGeneralConversation($profile);

        return $conversation;
    }
}