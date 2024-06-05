<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\ItemsList\Order\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Search
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Order\Customer\Search
{
    /**
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return false;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('No orders');
    }

    /**
     * getEmptyListFile
     *
     * @return string
     */
    protected function getEmptyListFile()
    {
        return '../modules/XC/CrispWhiteSkin/items_list/order/empty.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'items_list/order/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
