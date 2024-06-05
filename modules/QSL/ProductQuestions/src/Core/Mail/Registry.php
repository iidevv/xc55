<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Registry extends \XLite\Core\Mail\Registry
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::ZONE_CUSTOMER => [
                'modules/QSL/ProductQuestions/answer' => ProductQuestionAnswerCustomer::class,
            ],
            \XLite::ZONE_ADMIN => [
                'modules/QSL/ProductQuestions/new_question' => NewProductQuestionAdmin::class,
            ],
        ]);
    }
}
