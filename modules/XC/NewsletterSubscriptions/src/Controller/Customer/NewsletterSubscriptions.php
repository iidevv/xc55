<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\Controller\Customer;

use XLite\Core\TopMessage;
use XLite\Core\Validator\String\Email;

/**
 * NewsletterSubscriptions controller
 */
class NewsletterSubscriptions extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return true;
    }

    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        $email = \XLite\Core\Request::getInstance()->newlettersubscription_email;

        try {
            (new Email(true))->validate($email);

            if (!$this->isSubscribedAlready($email)) {
                $this->doSubscribe($email);
            }
        } catch (\XLite\Core\Validator\Exception $e) {
            TopMessage::addError($e->getMessage());
        }

        $this->setPureAction();
    }

    /**
     * Check if passed email already in subscription
     *
     * @param  string  $email Email
     *
     * @return boolean
     */
    protected function isSubscribedAlready($email)
    {
        return (bool) \XLite\Core\Database::getRepo('XC\NewsletterSubscriptions\Model\Subscriber')
            ->findOneByEmail($email);
    }

    /**
     * Create subscriber
     *
     * @param  string  $email Email
     */
    protected function doSubscribe($email)
    {
        $subscriber = new \XC\NewsletterSubscriptions\Model\Subscriber();
        $subscriber->setEmail($email);

        if (\XLite\Core\Auth::getInstance()->getProfile()) {
            $subscriber->setProfile(
                \XLite\Core\Auth::getInstance()->getProfile()
            );
        }

        \XLite\Core\Database::getEM()->persist($subscriber);
        \XLite\Core\Database::getEM()->flush($subscriber);
    }
}
