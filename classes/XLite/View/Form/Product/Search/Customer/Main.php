<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Search\Customer;

/**
 * Main
 */
class Main extends \XLite\View\Form\Product\Search\Customer\ACustomer
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'search';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'search';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + [
            'searchInSubcats'   => 'Y',
            'itemsList'         => '\XLite\View\ItemsList\Product\Customer\Search',
        ];
    }
}
