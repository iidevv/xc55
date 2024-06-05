<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Controller\Admin;

use XLite\Core\TopMessage;

class BannersList extends \XLite\Controller\Admin\Settings
{
    public const PERMISSION_BANNERS = 'manage banners';

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed(static::PERMISSION_BANNERS);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Banners');
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    public function getLocation()
    {
        return static::t('Banners');
    }

    /**
     * Update banners list
     */
    public function doActionUpdate()
    {
        $oldCount = \XLite\Core\Database::getRepo('QSL\Banner\Model\Banner')->count() ?: 0;
        $list     = new \QSL\Banner\View\ItemsList\Model\Banner();
        $list->processQuick();
        $newCount = \XLite\Core\Database::getRepo('QSL\Banner\Model\Banner')->count() ?: 0;

        if ($oldCount !== $newCount) {
            $controller = new \QSL\Banner\Controller\Admin\CacheManagement();
            $controller->rebuildViewLists();
        }
        TopMessage::addInfo('The banners list has been successfully updated');
    }
}
