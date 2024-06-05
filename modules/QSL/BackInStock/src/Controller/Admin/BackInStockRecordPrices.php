<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

/**
 * Price drop subscriptions controller
 */
class BackInStockRecordPrices extends \QSL\BackInStock\Controller\Admin\ABackInStockRecords
{
    /**
     * @return string
     */
    public function getLocation()
    {
        return static::t('Price drop');
    }

    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: '\QSL\BackInStock\View\ItemsList\Model\RecordPrice';
    }
}
