<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * OrderItem items list
 * @Extender\Mixin
 */
class OrderItem extends \XLite\View\ItemsList\Model\OrderItem
{
    /**
     * Get request data
     *
     * @return array
     */
    protected function defineRequestData()
    {
        $requestData = parent::defineRequestData();

        $request = \XLite\Core\Request::getInstance();

        if (! empty($request->is_initiated_by_partial_return)) {
            if (isset($request->order_items)) {
                $requestData['order_items'] = $request->order_items;
            }

            if (isset($request->delete_order_items)) {
                $requestData['delete_order_items'] = $request->delete_order_items;
            }

            if (isset($request->auto)) {
                $requestData['auto'] = $request->auto;
            }
        }

        if (isset($requestData['items'])) {
            unset($requestData['items']);
        }

        return $requestData;
    }
}
