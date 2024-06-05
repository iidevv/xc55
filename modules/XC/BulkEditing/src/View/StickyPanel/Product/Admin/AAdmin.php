<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\View\StickyPanel\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product list sticky panel
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\StickyPanel\Product\Admin\AAdmin
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['bulk_edit'] = [
            'class'    => 'XC\BulkEditing\View\Button\Product',
            'params'   => [
                'style'          => 'more-action always-enabled',
                'dropDirection'  => 'dropup',
            ],
            'position' => 50,
        ];

        return $list;
    }
}
