<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\ItemsList\Model\Order\Admin;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 *
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    public const PARAM_ORDER_IN_PROGRESS = 'inProgress';

    public static function getSearchParams()
    {
        return parent::getSearchParams() + [
                \Qualiteam\SkinActCreateOrder\Model\Repo\Order::P_ORDER_IN_PROGRESS => static::PARAM_ORDER_IN_PROGRESS,
            ];
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER_IN_PROGRESS => new \XLite\Model\WidgetParam\TypeString('Order inProgress', '1'),
        ];
    }

    public static function getSearchValuesStorage($forceFallback = false)
    {
        $storage = parent::getSearchValuesStorage($forceFallback);

        $inProgress = (\XLite::getController() instanceof \Qualiteam\SkinActCreateOrder\Controller\Admin\OrdersInProgress);

        $storage->setValue(static::PARAM_ORDER_IN_PROGRESS, $inProgress);

        return $storage;
    }

    protected function getTopActions()
    {
        $actions = parent::getTopActions();

        $actions[] = 'modules/Qualiteam/SkinActCreateOrder/create_order.twig';

        return $actions;
    }

    public function displayCommentedData(array $data)
    {
        $data['filtered'] = !empty(\XLite\Core\Session::getInstance()->{static::getSessionCellName()});
        parent::displayCommentedData($data);
    }

}