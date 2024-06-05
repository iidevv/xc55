<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use QSL\ProductQuestions\Core\Mail\ProductQuestionAnswerCustomer;
use QSL\ProductQuestions\Core\Mail\NewProductQuestionAdmin;

/**
 * Decorated Mailer class.
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    /**
     * Notify the customer about the new answer on his/her product question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question model
     *
     * @return void
     */
    public static function sendProductQuestionAnswerCustomer(\QSL\ProductQuestions\Model\Question $question)
    {
        $profile = $question->getProfile();
        $email   = $question->getEmail();

        if (!$email) {
            $email = $profile ? $profile->getLogin() : false;
        }

        if (empty($email) || empty($question->getAnswer())) {
            return;
        }

        static::getBus()->dispatch(new SendMail(ProductQuestionAnswerCustomer::class, [$question, $profile, $email]));
    }

    /**
     * Notify the store owner about new product question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question model
     *
     * @return void
     */
    public static function sendNewProductQuestionAdmin(\QSL\ProductQuestions\Model\Question $question)
    {
        static::getBus()->dispatch(new SendMail(NewProductQuestionAdmin::class, [$question]));
    }

    /**
     * Split an UTF-8 string into multiple lines by words.
     *
     * @param string  $string UTF-8 string
     * @param integer $width  Maximum number of characters per line OPTIONAL
     * @param string  $break  Line-break character OPTIONAL
     * @param boolean $cut    Whether to break long words, or not OPTIONAL
     *
     * @return string
     */
    public static function wrapUtf8StringByWords($string, $width = 75, $break = "\n", $cut = false)
    {
        if ($cut) {
            $search = '/(.{1,' . $width . '})(?:\s|$)|(.{' . $width . '})/uS';
            $replace =  '$1$2' . $break;
        } else {
            $search = '/(?=\s)(.{1,' . $width . '})(?:\s|$)/uS';
            $replace = '$1' . $break;
        }

        $lines = explode("\n", $string);
        $r = '';
        foreach ($lines as $line) {
            $r .= $break . preg_replace($search, $replace, $line);
        }

        return $r;
    }

    /**
     * Returns the e-mail of the person who is to reply on the product question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Product question
     *
     * @return string
     */
    public static function getQuestionVendorMail(\QSL\ProductQuestions\Model\Question $question)
    {
        $config = \XLite\Core\Config::getInstance()->Company;

        return $config->product_questions_admin_email
            ?: static::getOrdersDepartmentMail();
    }
}
