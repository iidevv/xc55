<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Mailer class.
 *
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiVendor", "QSL\ProductQuestions"})
 */
class MailerWithMultiVendor extends \XLite\Core\Mailer
{
   /**
     * Returns the e-mail of the person who is to reply on the product question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Product question
     *
     * @return string
     */
    public static function getQuestionVendorMail(\QSL\ProductQuestions\Model\Question $question)
    {
        $product = $question->getProduct();
        $vendorProfile = $product ? $product->getVendor() : null;

        return $vendorProfile ? $vendorProfile->getLogin() : parent::getQuestionVendorMail($question);
    }
}
