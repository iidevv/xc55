<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Menu\Admin;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Core\View\DynamicWidgetInterface;


class InProgressOrdersCount extends \XLite\View\Menu\Admin\LeftMenu\ANode implements DynamicWidgetInterface
{
    use ExecuteCachedTrait;

    protected function getLabel()
    {
        return $this->executeCachedRuntime(function () {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->inProgress = true;

            return Database::getRepo(\XLite\Model\Order::class)->search($cnd, true) ?: false;
        }, [
            self::class,
            __METHOD__,
        ]);
    }

    protected function getCacheParameters()
    {
        return array_merge(parent::getCacheParameters(), [
            Database::getRepo(\XLite\Model\Order::class)->getVersion()
        ]);
    }
}
