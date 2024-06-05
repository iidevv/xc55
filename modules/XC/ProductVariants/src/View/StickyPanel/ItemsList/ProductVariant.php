<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\StickyPanel\ItemsList;

/**
 * Product variant items list's sticky panel
 */
class ProductVariant extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'action'     => 'deleteVariants',
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
        ];
    }
}
