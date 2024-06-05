<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Promotions extends \XLite\Controller\Admin\Promotions
{
    /**
     * Page key
     */
    public const PAGE_SALE_DISCOUNTS = 'sale_discounts';

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();

        $list[static::PAGE_SALE_DISCOUNTS] = [
            'name'       => static::t('Sale promotions'),
            'tpl'        => 'modules/CDev/Sale/sale_discounts/body.twig',
            'permission' => 'manage sale discounts',
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
            || ($this->getPage() === static::PAGE_SALE_DISCOUNTS
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage sale discounts')
            );
    }

    /**
     * Update list
     */
    protected function doActionSaleDiscountsUpdate()
    {
        $list = new \CDev\Sale\View\ItemsList\SaleDiscounts();
        $list->processQuick();
    }

    /**
     * @return bool
     */
    public function shouldShowSaleQuickDataWarning()
    {
        return true;
    }
}
