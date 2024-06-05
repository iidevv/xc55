<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\Config;
use XLite\View\FormField\Select\EmailFrom;

/**
 * \XLite\View\TestEmail
 */
class TestEmail extends \XLite\View\Dialog
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'test_email';
    }

    /**
     * @return string
     */
    protected function getMailTesterArticleLink()
    {
        return 'https://support.x-cart.com/en/articles/4570131-testing-email-transfer-settings-with-mail-tester-com';
    }

    /**
     * @return string
     */
    protected function getCompanyName()
    {
        return Config::getInstance()->Company->company_name;
    }

    /**
     * @return bool
     */
    protected function isContactMode()
    {
        return Config::getInstance()->Email->mail_from_type === EmailFrom::OPTION_FROM_CONTACT;
    }

    /**
     * @return bool
     */
    protected function isServerMode()
    {
        return Config::getInstance()->Email->mail_from_type === EmailFrom::OPTION_FROM_SERVER;
    }

    /**
     * @return bool
     */
    protected function isManualMode()
    {
        return Config::getInstance()->Email->mail_from_type === EmailFrom::OPTION_MANUAL;
    }

    /**
     * @return string
     */
    protected function getManualMail()
    {
        return Config::getInstance()->Email->mail_from_manual;
    }

    /**
     * @return array
     */
    protected function getContactEmailsAsOptions()
    {
        $list = array_unique([
            \XLite\Core\Mailer::getSiteAdministratorMail(false),
            \XLite\Core\Mailer::getUsersDepartmentMail(false),
            \XLite\Core\Mailer::getOrdersDepartmentMail(false),
            \XLite\Core\Mailer::getSupportDepartmentMail(false),
        ]);

        return array_reduce($list, function ($acc, $email) {
            $acc[$email] = $this->getCompanyName() . ' ' . $email;
            return $acc;
        }, []);
    }
}
