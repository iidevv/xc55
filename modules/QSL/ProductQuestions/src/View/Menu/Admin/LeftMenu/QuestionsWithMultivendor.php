<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Menu\Admin\LeftMenu;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiVendor", "QSL\ProductQuestions"})
 */
class QuestionsWithMultivendor extends Questions
{
    /**
     * Counts the number of unanswered questions.
     *
     * @return integer
     */
    protected function getCountUnansweredQuestions()
    {
        $auth = \XLite\Core\Auth::getInstance();

        return $auth->isVendor()
            ? (int) \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question')->searchUnansweredVendorQuestions(
                true,
                $auth->getProfile()
            )
            : parent::getCountUnansweredQuestions();
    }
}
