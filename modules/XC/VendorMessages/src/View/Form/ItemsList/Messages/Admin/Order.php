<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Form\ItemsList\Messages\Admin;

/**
 * Admin order messages
 */
class Order extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'update_messages';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['order_number'] = $this->getOrder()->getOrderNumber();
        $list['page'] = 'messages';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' order-messages');
    }
}
