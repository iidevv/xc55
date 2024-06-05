<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Controller\Admin;

use Qualiteam\SkinActVideoTour\View\ItemsList\Model\VideoTour as VideoToursItemsList;
use XLite\Core\Auth;

/**
 * Class video tours
 */
class VideoTours extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL(): bool
    {
        return parent::checkACL()
            || Auth::getInstance()->isPermissionAllowed('manage catalog');
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass(): string
    {
        return parent::getItemsListClass()
            ?: VideoToursItemsList::class;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle(): string
    {
        return static::t('SkinActVideoTour video tours');
    }

    /**
     * Return null since it's common videos list
     *
     * @return integer
     */
    public function getProductId(): int
    {
        return 0;
    }
}