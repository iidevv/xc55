<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\Form;

/**
 * Contact us form
 */
class CustomerSurvey extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'customer_survey';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'send';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return [
            'id'     => \XLite\Core\Request::getInstance()->id,
            'rating' => \XLite\Core\Request::getInstance()->rating,
            'key'    => \XLite\Core\Request::getInstance()->key,
        ];
    }
}
