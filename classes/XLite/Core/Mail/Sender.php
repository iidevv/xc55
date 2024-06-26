<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail;

use XLite\View\Mailer;

abstract class Sender
{
    protected static $mailer;

    /**
     * @return \XLite\View\Mailer
     */
    public static function getMailer()
    {
        if (is_null(static::$mailer)) {
            static::$mailer = new \XLite\View\Mailer();
        }

        return static::$mailer;
    }

    public static function resetMailer()
    {
        static::$mailer = null;
    }

    /**
     * @param AMail $mail
     * @param array $processors
     *
     * @return Mailer
     */
    protected static function getMailerForMail(AMail $mail, array $processors = [])
    {
        $mailer = $mail->isSeparateMailer() ? $mail->prepareSeparateMailer(new Mailer()) : static::getMailer();

        foreach ($processors as $processor) {
            $mailer = $processor($mailer);
        }

        return $mailer;
    }

    /**
     * @param AMail $mail
     *
     * @return \Closure
     */
    protected static function getDataProcessor(AMail $mail)
    {
        return static function (Mailer $mailer) use ($mail) {
            static::register($mailer, $mail->getData());

            return $mailer;
        };
    }

    /**
     * @param AMail $mail
     *
     * @return \Closure
     */
    protected static function getAttachmentProcessor(AMail $mail)
    {
        return static function (Mailer $mailer) use ($mail) {
            foreach ($mail->getStringAttachments() as $attachment) {
                @[$content, $name, $encoding, $mime] = $attachment;
                $mailer->addStringAttachment($content, $name, $encoding, $mime);
            }

            foreach ($mail->getAttachments() as $attachment) {
                @[$path, $name, $encoding, $mime] = $attachment;
                $mailer->addAttachment($path, $name, $encoding, $mime);
            }

            return $mailer;
        };
    }

    /**
     * @param AMail $mail
     *
     * @return \Closure
     */
    protected static function getComposeProcessor(AMail $mail)
    {
        return static function (Mailer $mailer) use ($mail) {
            $mailer->compose(
                $mail,
                static::getVariablesPopulateProcessor($mail)
            );

            return $mailer;
        };
    }

    /**
     * @param AMail $mail
     *
     * @return bool
     * @throws \phpmailerException
     */
    public static function send(AMail $mail)
    {
        if (\Includes\Utils\ConfigParser::getOptions(['performance', 'background_jobs']) || !Registry::isSent($mail->getHash())) {
            $mailer = static::getMailerForMail(
                $mail,
                [
                    static::getDataProcessor($mail),
                    static::getAttachmentProcessor($mail),
                    static::getComposeProcessor($mail)
                ]
            );

            if ($mailer->send()) {
                Registry::addToSent($mail->getHash());
                $mail->handleSendSuccess();
                return true;
            }
            $mail->handleSendError($mailer->getLastError(), $mailer->getLastErrorMessage());

            $mailer->clearAttachments();
            $mailer->clearStringAttachments();

            return false;
        }

        return true;
    }

    /**
     * @param Mailer $mailer
     * @param array  $data
     */
    protected static function register(Mailer $mailer, array $data)
    {
        foreach ($data as $k => $v) {
            $mailer->set($k, $v ?? false);
        }
    }

    /**
     * @param AMail $mail
     *
     * @return \Closure
     */
    protected static function getVariablesPopulateProcessor(AMail $mail)
    {
        return static function ($content) use ($mail) {
            $names = array_keys($mail::getVariables());
            $variables = [];

            foreach ($names as $key => $name) {
                $variables[$key] = $mail->getVariable($name);
                $names[$key] = "%{$name}%";
            }

            return str_replace($names, $variables, $content);
        };
    }
}
