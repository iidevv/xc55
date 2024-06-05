<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Form\Question\Customer;

/**
 * Add/edit review form
 */
class Question extends \XLite\View\Form\AForm
{
    /**
     * Widget params names
     */
    public const PARAM_ID         = 'id';
    public const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product_question';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'create';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return [
            self::PARAM_ID              => \XLite\Core\Request::getInstance()->id,
            self::PARAM_PRODUCT_ID      => \XLite\Core\Request::getInstance()->product_id,
        ];
    }
}
