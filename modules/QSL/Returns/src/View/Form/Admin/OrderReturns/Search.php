<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Form\Admin\OrderReturns;

/**
 * Search returns form
 */
class Search extends \XLite\View\Form\AForm
{
    /**
     * Return default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'order_returns';
    }

    /**
     * Return default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'search';
    }

    /**
     * Return default params
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + ['mode' => 'search'];
    }
}
