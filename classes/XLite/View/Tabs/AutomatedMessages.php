<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class AutomatedMessages extends \XLite\View\Tabs\ATabs
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'notifications';
        $list[] = 'notification_common';
        $list[] = 'notification_attachments';
        $list[] = 'test_email';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'notifications'            => [
                'weight' => 100,
                'title'  => static::t('Notifications'),
                'widget' => 'XLite\View\ItemsList\Model\Notification',
            ],
            'notification_common'            => [
                'weight' => 110,
                'title'    => static::t('Header, Greeting & Signature'),
                'template' => 'notifications/common.twig',
            ],
            'notification_attachments' => [
                'weight'   => 300,
                'title'    => static::t('Attachments'),
                'template' => 'notifications/attachments.twig',
            ],
        ];
    }
}
