<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Core;

use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActQuickbooks\Core\Mail\SendEmailOrdersErrorsMessage;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * Send emails about orders import errors
     *
     * @param string|array $body Email
     *
     * @return bool
     */
    public static function sendEmailAboutOrdersImportErrors($body)
    {
        $result = false;

        if ($body) {
            $result = (new SendEmailOrdersErrorsMessage($body))->send();
        }

        return $result;
    }
}