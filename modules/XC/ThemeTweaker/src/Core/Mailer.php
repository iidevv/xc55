<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Exception;
use XLite\Core\Mail\Registry;
use XLite\Core\Session;

/**
 * Mailer
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    /**
     * @param $templatesDirectory
     *
     * @return bool
     */
    protected static function isAttachPdfInvoice($templatesDirectory)
    {
        return static::isOrderNotification($templatesDirectory)
            && \XLite\Core\Config::getInstance()->NotificationAttachments->attach_pdf_invoices;
    }

    /**
     * @param $templateDirectory
     *
     * @return bool
     */
    public static function isOrderNotification($templateDirectory)
    {
        return in_array(
            $templateDirectory,
            [
                'order_canceled',
                'order_changed',
                'order_created',
                'order_failed',
                'order_processed',
                'order_shipped',
                'order_tracking_information',
            ],
            true
        );
    }

    /**
     * Send created order mail to customer
     *
     * @param string $templatesDirectory
     * @param string $to
     * @param string $zone
     * @param array  $data
     *
     * @return bool
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function sendNotificationPreview($templatesDirectory, $to, $zone, array $data)
    {
        $mail = Registry::createNotification($zone, $templatesDirectory, $data);
        $mail->setLanguageCode(Session::getInstance()->getCurrentLanguage());
        $mail->setTo($to);

        if (!$mail) {
            throw new Exception(sprintf("Undefined email notification: %s/%s", $zone, $templatesDirectory));
        }

        return $mail->send();
    }
}
