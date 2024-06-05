<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Core\Mail;

use XLite\Core\Mailer;

class ProductQuestionAnswerCustomer extends \XLite\Core\Mail\AMail
{
    public function __construct(\QSL\ProductQuestions\Model\Question $question, $profile, $email)
    {
        parent::__construct();

        $this->setFrom(Mailer::getQuestionVendorMail($question));

        $this->setTo($email);

        $firstname = $question->getName();
        if (!$firstname) {
            $firstname = $profile ? $profile->getName() : '';
        }


        $this->appendData([
            'question'     => $question,
            'profile'      => $profile,
            'product'      => $question->getProduct(),
            'name'         => ['firstname' => $firstname],
            'answer'       => $question->getAnswer(),
            'questionText' => Mailer::wrapUtf8StringByWords($question->getQuestion(), 75, "\n> "),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    /**
     * @inheritDoc
     */
    public static function getDir()
    {
        return 'modules/QSL/ProductQuestions/answer';
    }

    // /**
    //  * @inheritDoc
    //  */
    // protected static function defineVariables()
    // {
    //     return [
    //             'order_return_reason' => static::t('Return reason'),
    //             'order_return_action' => static::t('Return action'),
    //             'return_action_text' => static::t('Return action text'),
    //             'order_return_comment' => static::t('Return comment'),
    //         ] + parent::defineVariables();
    // }
}
