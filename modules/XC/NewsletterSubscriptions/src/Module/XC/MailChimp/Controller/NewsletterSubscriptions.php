<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\Module\XC\MailChimp\Controller;

use XCart\Extender\Mapping\Extender;
use XC\MailChimp\Core;

/**
 * NewsletterSubscriptions controller
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MailChimp")
 */
class NewsletterSubscriptions extends \XC\NewsletterSubscriptions\Controller\Customer\NewsletterSubscriptions
{
    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        if ($this->isMailChimpConfigured()) {
            $this->doSubscribeToMailChimp();
        } else {
            parent::doActionSubscribe();
        }
    }

    /**
     * Check if MailChimp module is configured and have lists
     *
     * @return boolean
     */
    protected function isMailChimpConfigured()
    {
        return \XC\MailChimp\Main::isMailChimpConfigured();
    }

    /**
     * Subscribe to mailchimp
     */
    protected function doSubscribeToMailChimp()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        $email = \XLite\Core\Request::getInstance()->newlettersubscription_email;
        $tempProfile = false;

        if (!$profile || $profile->getLogin() !== $email) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);
            if (!$profile) {
                $profile = $this->getNewProfileToSubscribe($email);
                $profile->create();
                \XLite\Core\Database::getEM()->persist($profile);
                $tempProfile = true;
            }
        }

        try {
            $subscribeResult = Core\MailChimp::processSubscriptionAll($profile);

            if (isset($subscribeResult['subscribe'])) {
                if ($subscribeResult['subscribe'] === 0) {
                    \XLite\Core\Event::changeSuccessMessage(['message' => static::t('You have already subscribed to our newsletters')]);
                } else {
                    \XLite\Core\Event::changeSuccessMessage(['message' => static::t(
                        'Thank you for subscribing to the newsletter! We hope you enjoy shopping at {{companyName}}',
                        [
                            'companyName' => \XLite\Core\Config::getInstance()->Company->company_name,
                        ]
                    )]);
                }
            }
        } catch (Core\MailChimpException $e) {
            $this->valid = false;
            \XLite\Core\TopMessage::addError(Core\MailChimp::getMessageTextFromError($e));
        }

        if ($tempProfile) {
            $profile->delete();
        }
    }

    /**
     * @param $email
     *
     * @return \XLite\Model\Profile
     */
    protected function getNewProfileToSubscribe($email)
    {
        $profileToSubscribe = new \XLite\Model\Profile();
        $profileToSubscribe->setLogin($email);
        $profileToSubscribe->setAnonymous(true);
        // WA to make doctrine doesn't load other 200k DB xlite_mailchimp_subscriptions rows for the related xlite_mailchimp_lists.id association
        // otherwise $tempProfile->delete() results in 'Allowed Memory Size Exhausted' error
        $profileToSubscribe->setStatusComment(\XLite\Model\Profile::MAILCHIMP_TEMP_PROFILE_COMMENT);
        return $profileToSubscribe;
    }
}
