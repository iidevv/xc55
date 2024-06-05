<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * @ListChild (list="dashboard.info_block.marketplace_notifications", weight="100", zone="admin")
 */
class MarketplaceNotifications extends \XLite\View\AView
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $result   = parent::getCSSFiles();
        $result[] = 'dashboard/info_block/notification/style.less';

        return $result;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $result   = parent::getJSFiles();
        $result[] = 'marketing_info/script.js';

        return $result;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/info_block/notification/marketplace_notifications.twig';
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL() && Auth::getInstance()->hasRootAccess();
    }
}
