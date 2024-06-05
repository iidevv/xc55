<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Promotions extends \XLite\Controller\Admin\Promotions
{
    /**
     * Page key
     */
    public const PAGE_VOLUME_DISCOUNTS = 'volume_discounts';

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list                                = parent::getPagesStatic();
        $list[static::PAGE_VOLUME_DISCOUNTS] = [
            'name'       => static::t('Volume discounts'),
            'tpl'        => 'modules/CDev/VolumeDiscounts/discounts/body.twig',
            'permission' => 'manage volume discounts',
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
            || ($this->getPage() === static::PAGE_VOLUME_DISCOUNTS
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage volume discounts')
            );
    }

    /**
     * Get currency formatted value
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return \XLite::getInstance()->getCurrency()->getCurrencySymbol();
    }

    /**
     * Update list
     */
    protected function doActionVolumeDiscountsUpdate()
    {
        $list = new \CDev\VolumeDiscounts\View\ItemsList\VolumeDiscounts();
        $list->processQuick();
    }
}
