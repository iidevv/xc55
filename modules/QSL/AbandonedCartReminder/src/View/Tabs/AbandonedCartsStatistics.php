<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs displayed on the Statistics back-end page.
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class AbandonedCartsStatistics extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'cart_recovery_stats',
                'cart_email_stats',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'cart_email_stats'    => [
                'weight'   => 100,
                'title'    => static::t('ACR Email Statistics'),
                'template' => 'modules/QSL/AbandonedCartReminder/email_stats/page.twig',
            ],
            'cart_recovery_stats' => [
                'weight'   => 200,
                'title'    => static::t('Cart recovery statistics'),
                'template' => 'modules/QSL/AbandonedCartReminder/recovery_stats/body.twig',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/QSL/AbandonedCartReminder/email_stats/table.less',
            ]
        );
    }
}
