<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\ItemsList\Messages\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * Admin order messages
 */
class Order extends \XC\VendorMessages\View\ItemsList\Messages\Admin\Order
{

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/order_messages/style.less';
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/uploader.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/messages/markReadUnread.js';
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/admin_uploader.js';

        return $list;
    }

    /**
     * getPageBodyTemplate
     *
     * @return string
     */
    protected function getPageBodyTemplate()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/order_messages/body.twig';
    }

    /**
     * Mark messages as read
     *
     * @return integer
     */
    protected function markMessagesAsRead()
    {
        return false;
    }

    protected function getEmptyListTemplate()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/order_messages/empty.twig';
    }

}