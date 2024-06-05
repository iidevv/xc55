<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Mail;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Mail\AMail;
use XLite\Core\Mail\Registry;
use XLite\View\Mailer;

/**
 * @Extender\Mixin
 */
class Sender extends \XLite\Core\Mail\Sender
{
    public static function getNotificationEditableContent($dir, $data, $zone)
    {
        $mail = Registry::createNotification($zone, $dir, $data);

        $mailer = static::getMailerForMail(
            $mail,
            [
                static::getDataProcessor($mail),
                static::getThemeTweakerMailerProcessor($mail),
            ]
        );

        $variablesProcessor = static::getVariablesPopulateProcessor($mail);

        return $variablesProcessor($mailer->getNotificationEditableContent($zone));
    }

    public static function getNotificationPreviewContent($dir, $data, $zone)
    {
        $mail   = Registry::createNotification($zone, $dir, $data);
        $mailer = static::getMailerForMail(
            $mail,
            [
                static::getDataProcessor($mail),
                static::getThemeTweakerMailerProcessor($mail),
            ]
        );

        $variablesProcessor = static::getVariablesPopulateProcessor($mail);

        return $variablesProcessor($mailer->getNotificationPreviewContent($zone));
    }

    protected static function getThemeTweakerMailerProcessor(AMail $mail)
    {
        return static function (Mailer $mailer) use ($mail) {
            $mailer->set('dir', $mail::getDir());

            return $mailer;
        };
    }
}
