<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

use XLite\Controller\Features\SearchByFilterTrait;

/**
 * Price drop products controller
 */
abstract class ABackInStockProducts extends \XLite\Controller\Admin\AAdmin
{
    use SearchByFilterTrait;

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Products');
    }

    /**
     * Update list
     */
    protected function doActionUpdate()
    {
        $class = $this->getItemsListClass();
        $list = new $class();
        $list->processQuick();
    }
}
