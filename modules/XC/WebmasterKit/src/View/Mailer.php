<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\View;

use XCart\Extender\Mapping\Extender;
use XLite\InjectLoggerTrait;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\View\Mailer
{
    use InjectLoggerTrait;

    /**
     * Send message
     *
     * @return boolean
     */
    public function send()
    {
        if (\XLite\Core\Config::getInstance()->XC->WebmasterKit->logMail) {
            $this->getLogger('mail-messages')->debug('', [
                'From'     => $this->get('to'),
                'To'       => $this->mail->From,
                'Subject'  => $this->mail->Subject,
                'Reply-To' => $this->prepareLogReplyTo($this->mail->getReplyToAddresses()),
                'Body'     => $this->mail->Body,
            ]);
        }

        return parent::send();
    }

    /**
     * @param array $replyTos
     *
     * @return string
     */
    protected function prepareLogReplyTo(array $replyTos)
    {
        $result = [];

        foreach ($replyTos as $replyTo) {
            $address = $replyTo[0];
            $name = $replyTo[1];

            $result[] = $name
                ? "{$name} <{$address}>"
                : $address;
        }

        return implode(PHP_EOL, $result);
    }
}
