<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCouponSearchBar\View\ItemsList\Model\Order\Admin;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{

    public static function getSearchParams()
    {
        return parent::getSearchParams() + [
                'couponId' => 'couponId',
            ];
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            'couponId' => new \XLite\Model\WidgetParam\TypeString('couponId', ''),
        ];
    }

}