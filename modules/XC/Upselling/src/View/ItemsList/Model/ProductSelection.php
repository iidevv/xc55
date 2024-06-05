<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\View\ItemsList\Model;

/**
 * Product selection itemlist model
 */
class ProductSelection extends \XLite\View\ItemsList\Model\ProductSelection
{
    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XC\Upselling\View\StickyPanel\ItemsList\ProductSelection';
    }

    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getFormOptions()
    {
        $options = parent::getFormOptions();

        $options['class'] = '\XC\Upselling\View\Form\ItemsList\ProductSelection\Table';

        return $options;
    }

    /**
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['product_id'] = \XLite\Core\Request::getInstance()->product_id;

        return $this->commonParams;
    }
}
