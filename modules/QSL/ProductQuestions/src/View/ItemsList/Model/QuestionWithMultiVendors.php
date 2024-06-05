<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Questions items list
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class QuestionWithMultiVendors extends \QSL\ProductQuestions\View\ItemsList\Model\Question
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $auth = \XLite\Core\Auth::getInstance();
        if ($auth->isVendor()) {
            $result->{\QSL\ProductQuestions\Model\Repo\Question::SEARCH_VENDOR_ID} = $auth->getVendorId();
        }

        return $result;
    }
}
