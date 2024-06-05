<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Customer;

use XLite\Core\Request;
use XLite\Core\Database;
use XLite\Core\Converter;
use XLite\Core\TopMessage;
use QSL\AbandonedCartReminder\Model\UnsubscribedUser;

/**
 * Abandoned cart controller.
 */
class AbandonedCart extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Unsubscribes from cart reminder e-mails.
     *
     * @return void
     */
    protected function doActionUnsubscribe()
    {
        $email = trim($this->getEmail());

        if ($email) {
            if ($this->isEmailAllowed($email)) {
                $unsubscribed = new UnsubscribedUser();
                $unsubscribed->setEmail(strtolower($email));
                $unsubscribed->setUnsubscribeDate(Converter::time());
                $unsubscribed->getRepository()->insert($unsubscribed);
            }

            TopMessage::addInfo(
                static::t(
                    'We will not remind the email address X about abandoned carts anymore.',
                    ['email' => $email]
                )
            );
        }

        // Set return URL
        $this->setReturnURL($this->buildURL(''));
    }

    /**
     * Get the e-mail address to unsubscribe from the request.
     *
     * @return integer
     */
    protected function getEmail()
    {
        $email = Request::getInstance()->email;

        return filter_var($email, \FILTER_VALIDATE_EMAIL);
    }

    /**
     * Checks if the specified e-mail can be added to the list of unsubscribed users.
     *
     * @param string $email Email to unsubscribe
     *
     * @return bool
     */
    protected function isEmailAllowed($email)
    {
        return !$this->getRepository()->findOneByEmail(strtolower($email));
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('\QSL\AbandonedCartReminder\Model\UnsubscribedUser');
    }
}
