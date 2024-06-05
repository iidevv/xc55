<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Core\Mail;

use XLite\Core\Mailer;

class NewProductQuestionAdmin extends \XLite\Core\Mail\AMail
{
    public function __construct(\QSL\ProductQuestions\Model\Question $question)
    {
        parent::__construct();

        $profile = $question->getProfile();

        $this->setFrom(Mailer::getSiteAdministratorMail());

        $this->setTo(Mailer::getQuestionVendorMail($question));

        $name = $question->getName();
        if (!$name) {
            $name = $profile ? $profile->getName() : '';
        }

        $this->appendData([
            'question' => $question,
            'profile'  => $profile,
            'product'  => $question->getProduct(),
            'name'     => $name,
            'url'      => \XLite\Core\Converter::buildFullURL(
                'product_question',
                '',
                [
                    'id' => $question->getId(),
                ],
                \XLite::getAdminScript()
            ),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    /**
     * @inheritDoc
     */
    public static function getDir()
    {
        return 'modules/QSL/ProductQuestions/new_question';
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
