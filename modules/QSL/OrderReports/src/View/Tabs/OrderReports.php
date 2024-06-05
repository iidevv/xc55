<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to user profile section
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class OrderReports extends \XLite\View\Tabs\ATabs
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'order_reports';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/QSL/OrderReports/css/style.less',
            ],
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'order_reports_total' => [
                'weight'     => 100,
                'title'      => static::t('Total'),
                'url_params' => [
                    'target' => 'order_reports',
                    'page'   => 'total',
                ],
                'template'   => 'modules/QSL/OrderReports/segment/total.twig',
            ],
            'order_reports_product' => [
                'weight'     => 200,
                'title'      => static::t('Product'),
                'url_params' => [
                    'target' => 'order_reports',
                    'page'   => 'product',
                ],
                'template'   => 'modules/QSL/OrderReports/segment/main.twig',
            ],
            'order_reports_category' => [
                'weight'     => 300,
                'title'      => static::t('Category'),
                'url_params' => [
                    'target' => 'order_reports',
                    'page'   => 'category',
                ],
                'template'   => 'modules/QSL/OrderReports/segment/main.twig',
            ],
            'order_reports_country' => [
                'weight'     => 400,
                'title'      => static::t('Shipping country'),
                'url_params' => [
                    'target' => 'order_reports',
                    'page'   => 'country',
                ],
                'template'   => 'modules/QSL/OrderReports/segment/main.twig',
            ],
            'order_reports_state' => [
                'weight'     => 500,
                'title'      => static::t('Shipping state'),
                'url_params' => [
                    'target' => 'order_reports',
                    'page'   => 'state',
                ],
                'template'   => 'modules/QSL/OrderReports/segment/main.twig',
            ],
            'order_reports_users' => [
                'weight'     => 600,
                'title'      => static::t('Top customers'),
                'url_params' => [
                    'target' => 'order_reports',
                    'page'   => 'users',
                ],
                'template'   => 'modules/QSL/OrderReports/segment/main.twig',
            ],
        ];
    }
}
