<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Config;
use XLite\View\FormField\Select\EmailFrom;

/**
 * Test e-mail controller
 */
class TestEmail extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Email transfer');
    }

    /**
     * Flag to has email error
     *
     * @return string
     */
    public function hasTestEmailError()
    {
        return (string)\XLite\Core\Session::getInstance()->test_email_error !== '';
    }

    /**
     * Return error test email sending
     *
     * @return string
     */
    public function getTestEmailError()
    {
        $error = (string)\XLite\Core\Session::getInstance()->test_email_error;

        \XLite\Core\Session::getInstance()->test_email_error = '';

        return $error;
    }

    /**
     * Action to send test email notification
     */
    protected function doActionTestEmail()
    {
        $request = \XLite\Core\Request::getInstance();

        $error = \XLite\Core\Mailer::sendTestEmail(
            static::getFromMail(),
            $request->test_to_email_address,
            $request->test_email_body
        );

        if ($error) {
            \XLite\Core\Session::getInstance()->test_email_error = $error;
            \XLite\Core\TopMessage::getInstance()->addError('Error of test e-mail sending: ' . $error);
        } else {
            \XLite\Core\TopMessage::getInstance()->add('Test e-mail have been successfully sent');
        }
    }

    /**
     * @return string
     */
    protected static function getFromMail()
    {
        $from = '';
        $mailFromType = Config::getInstance()->Email->mail_from_type;

        if (
            $mailFromType === EmailFrom::OPTION_FROM_CONTACT
            && \XLite\Core\Request::getInstance()->test_from_email_address
        ) {
            $from = \XLite\Core\Request::getInstance()->test_from_email_address;
        } elseif ($mailFromType === EmailFrom::OPTION_MANUAL) {
            $from = Config::getInstance()->Email->mail_from_manual;
        }

        return (string) $from;
    }
}
