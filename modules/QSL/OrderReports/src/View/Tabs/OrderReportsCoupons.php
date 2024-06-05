<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 * @Extender\After ("QSL\OrderReports")
 */
class OrderReportsCoupons extends \QSL\OrderReports\View\Tabs\OrderReports
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['order_reports_coupon'] = [
            'weight'     => 550,
            'title'      => static::t('Coupon'),
            'url_params' => [
                'target' => 'order_reports',
                'page'   => 'coupon',
            ],
            'template'   => 'modules/QSL/OrderReports/segment/main.twig',
        ];

        return $list;
    }
}
