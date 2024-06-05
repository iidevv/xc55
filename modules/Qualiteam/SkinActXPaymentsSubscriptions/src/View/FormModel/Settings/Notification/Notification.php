<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\FormModel\Settings\Notification;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Mailer;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Notification extends \XLite\View\FormModel\Settings\Notification\Notification
{
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $buttonsToExclude = ['preview', 'send_test_email'];

        $templatesDirectory = Request::getInstance()->getData()['templatesDirectory'];

        if (false !== strpos($templatesDirectory, Mailer::SUBSCRIPTION_PATH_PREFIX)) {
            foreach ($result as $key => $value) {
                if (in_array($key, $buttonsToExclude)) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }
}
