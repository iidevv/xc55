<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Form\Review\Admin;

/**
 * Add/edit review form
 */
class Review extends \XLite\View\Form\AForm
{
    /**
     * Widget params names
     */
    public const PARAM_ID                  = 'id';
    public const PARAM_TARGET_PRODUCT_ID   = 'target_product_id';

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'review';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = [
            self::PARAM_ID                  => \XLite\Core\Request::getInstance()->id,
            self::PARAM_TARGET_PRODUCT_ID   => \XLite\Core\Request::getInstance()->target_product_id,
        ];

        return $params;
    }
}
