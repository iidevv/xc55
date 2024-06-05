<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\Form;

/**
 * Sale discounts form
 */
class SaleDiscounts extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'promotions';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'sale_discounts_update';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['page'] = $this->page;

        return $list;
    }
}
