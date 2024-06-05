<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\PDFInvoice\View\StickyPanel\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Search order list sticky panel
 * @Extender\Mixin
 */
abstract class Search extends \XLite\View\StickyPanel\Order\Admin\Search
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list['pdf']  = [
            'class'    => 'QSL\PDFInvoice\View\Button\PDFSelectedInvoices',
            'params'   => [
                'disabled'   => true,
                'icon-style' => 'fa fa-download',
                'style'         => 'more-action hide-on-disable hidden',
                'showCaret'     => false,
                'dropDirection' => 'dropup',
            ],
            'position' => 300,
        ];

        return $list;
    }
}
