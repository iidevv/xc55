<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View;

use XCart\Extender\Mapping\Extender;

/**
 * Widget displaying product questions visible to the user.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class ProductQuestionsWithMultiVendors extends \QSL\ProductQuestions\View\ProductQuestions
{
    /**
     * Returns the full name of the user answered the question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question model
     *
     * @return string
     */
    protected function getFullAnswerName(\QSL\ProductQuestions\Model\Question $question)
    {
        $profile = $question->getAnswerProfile();

        $name = ($profile->isVendor() && ($profile->getName() === static::t('n/a')))
            ? $profile->getVendorCompanyName()
            : parent::getFullAnswerName($question);

        return $name ?: $this->getDefaultAnswerName($question);
    }
}
