<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\Form\ProductsReturn;

/**
 * Search returns form
 */
class Search extends \XC\CanadaPost\View\Form\ProductsReturn\AProductsReturn
{
    /**
     * Return default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'capost_returns';
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
