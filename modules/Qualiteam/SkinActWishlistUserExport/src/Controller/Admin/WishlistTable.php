<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActWishlistUserExport\Controller\Admin;


class WishlistTable extends \XLite\Controller\Admin\AAdmin
{
    public function getTitle()
    {
        return static::t('SkinActWishlistUserExport WishlistTable');
    }

    protected function doActionClearSearch()
    {
        $name = \Qualiteam\SkinActWishlistUserExport\View\ItemsList\Model\WishlistTable::getSessionCellName() . '_search';
        \XLite\Core\Session::getInstance()->{$name} = [];

        $name = \Qualiteam\SkinActWishlistUserExport\View\ItemsList\Model\WishlistTable::getSessionCellName() . '_processed';
        \XLite\Core\Session::getInstance()->{$name} = [];

        $this->setReturnURL($this->getURL());
    }
}