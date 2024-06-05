<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Notification details page
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Notification extends \XLite\View\Tabs\ATabs
{
    public static function getAllowedTargets()
    {
        return [
            'notification',
        ];
    }

    protected function defineTabs()
    {
        $list = [];

        $notification = $this->getNotification();

        if ($notification->getAvailableForCustomer() || $notification->getEnabledForCustomer()) {
            $list['customer'] = [
                'weight'   => 100,
                'title'    => static::t('notification.tab.customer'),
                'url_params' => ['target' => 'notification', 'templatesDirectory' => $notification->getTemplatesDirectory(), 'page' => 'customer'],
                'template' => 'notification/body.twig',
            ];
        }

        if ($notification->getAvailableForAdmin() || $notification->getEnabledForAdmin()) {
            $list['admin'] = [
                'weight'   => 200,
                'title'    => static::t('notification.tab.administrator'),
                'url_params' => ['target' => 'notification', 'templatesDirectory' => $notification->getTemplatesDirectory(), 'page' => 'admin'],
                'template' => 'notification/body.twig',
            ];
        }

        return $list;
    }

    /**
     * @return \XLite\Model\Notification
     */
    protected function getNotification()
    {
        return \XLite::getController()->getNotification();
    }
}
