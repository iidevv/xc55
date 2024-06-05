<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\Menu\Admin;

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
        if (!isset($this->relatedTargets['coupons'])) {
            $this->relatedTargets['coupons'] = [];
        }

        $this->relatedTargets['coupons'][] = 'coupon';

        parent::__construct($params);

        $this->addRelatedTarget('coupon', 'promotions', [], ['page' => 'coupons']);
    }
}
