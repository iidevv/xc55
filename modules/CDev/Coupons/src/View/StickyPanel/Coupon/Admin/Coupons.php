<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\StickyPanel\Coupon\Admin;

use XLite\Core\Database;

/**
 * Coupons sticky panel
 */
class Coupons extends \XLite\View\StickyPanel\ItemsListForm
{
    protected function getModuleSettingURL(): string
    {
        return parent::getModuleSettingURL() ?: $this->buildURL('module', '', ['moduleId' => 'CDev-Coupons']);
    }

    /**
     * Disable "save" button if the list is empty.
     */
    protected function defineButtons(): array
    {
        $list = parent::defineButtons();
        if (!Database::getRepo('CDev\Coupons\Model\Coupon')->count()) {
            unset($list['save']);
        }
        return $list;
    }
}
