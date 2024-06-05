<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\ItemsList\Model\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Search order
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    /**
     * Widget param names
     */
    public const PARAM_MESSAGES = \XLite\Model\Repo\Order::SEARCH_MESSAGES;

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        $list = parent::getSearchParams();
        $list[\XLite\Model\Repo\Order::SEARCH_MESSAGES] = static::PARAM_MESSAGES;

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_MESSAGES => new \XLite\Model\WidgetParam\TypeString('Messages condition', ''),
        ];
    }
}
