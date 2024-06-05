<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Product;

/**
 * Attributes
 */
class Attributes extends \XLite\View\StickyPanel\Product\AProduct
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = [];

        if (\XLite\Core\Request::getInstance()->spage === 'global') {
            $list['saveMode'] = $this->getWidget(
                [
                    'fieldName'  => 'save_mode',
                    'attributes' => [
                        'class'    => 'not-significant',
                        'disabled' => true,
                    ],
                    'value'      => false,
                    'label'      => static::t("Apply attribute value changes for all the products")
                ],
                'XLite\View\FormField\Input\Checkbox\Simple'
            );
        }

        return array_merge(parent::defineButtons(), $list);
    }
}
