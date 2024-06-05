<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Core;


use Qualiteam\SkinActCareers\Core\Mail\InterviewQuestions;
use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;

/**
 * Decorated Mailer class.
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    public static function sendNotificationInterviewQuestions(int $jobId, array $questionsData, array $files)
    {
        static::getBus()->dispatch(new SendMail(InterviewQuestions::class, [$jobId, $questionsData, $files]));
    }
}