<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\ItemsList\Model;

/**
 * Sale discount products items list for product selection popup
 */
class SaleDiscountProductSelection extends \XLite\View\ItemsList\Model\ProductSelection
{
    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getFormOptions()
    {
        $options = parent::getFormOptions();

        $options['class'] = \CDev\Sale\View\Form\ItemsList\ProductSelection\Table::class;

        return $options;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['sale_discount_id'] = \XLite\Core\Request::getInstance()->sale_discount_id;

        return $this->commonParams;
    }
}
