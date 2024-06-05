<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

/**
 * Back in stock subscriptions controller
 */
class BackInStockRecords extends \QSL\BackInStock\Controller\Admin\ABackInStockRecords
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Product requests');
    }

    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: '\QSL\BackInStock\View\ItemsList\Model\Record';
    }
}
