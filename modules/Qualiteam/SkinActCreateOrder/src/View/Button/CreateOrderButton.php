<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Button;


use XLite\Core\Request;

class CreateOrderButton extends \XLite\View\Button\AButton
{

    protected function isVisible()
    {
        return parent::isVisible() && in_array(Request::getInstance()->target, ['order_list', 'orders_in_progress'], true);
    }

    protected function getButtonAttributes()
    {
        $list = parent::getButtonAttributes();
        $list['data-fid'] = \XLite::getFormId();
        return $list;
    }

    protected function getClass()
    {
        return parent::getClass() . ' create-order-button';
    }

    protected function getButtonLabel()
    {
        return static::t('SkinActCreateOrder Create New Order');
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/create_order.js';
        return $list;
    }
}