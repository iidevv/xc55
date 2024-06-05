<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['order_list'])) {
            $this->relatedTargets['order_list'] = [];
        }

        if (!isset($this->relatedTargets['notifications'])) {
            $this->relatedTargets['notifications'] = [];
        }

        if (\XLite\Core\Request::getInstance()->section === 'orders_n_payments') {
            $this->relatedTargets['order_list'][] = 'abandoned_carts';
        } else {
            $this->relatedTargets['notifications'][] = 'abandoned_carts';
        }

        $this->relatedTargets['notifications'][]    = 'cart_reminders';
        $this->relatedTargets['cart_email_stats'][] = 'cart_recovery_stats';

        parent::__construct($params);
    }
}
