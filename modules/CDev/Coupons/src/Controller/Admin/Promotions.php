<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Promotions extends \XLite\Controller\Admin\Promotions
{
    /**
     * Page key
     */
    public const PAGE_COUPONS = 'coupons';

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();

        $list[static::PAGE_COUPONS] = [
            'name'       => static::t('Coupons'),
            'tpl'        => 'modules/CDev/Coupons/coupons/body.twig',
            'permission' => 'manage coupons',
            'weight'     => 100,
        ];

        return $list;
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL()
            || ($this->getPage() === static::PAGE_COUPONS
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage coupons')
            );
    }

    /**
     * Update list
     */
    protected function doActionCouponsUpdate()
    {
        $list = new \CDev\Coupons\View\ItemsList\Coupons();
        $list->processQuick();
    }
}
