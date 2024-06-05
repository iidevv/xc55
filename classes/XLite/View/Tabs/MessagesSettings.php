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
class MessagesSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'email_settings';
        $list[] = 'test_email';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'email_settings'           => [
                'weight' => 400,
                'title'  => static::t('Configuration'),
                'widget' => 'XLite\View\Model\Settings',
            ],
            'test_email'               => [
                'weight' => 500,
                'title'  => static::t('Testing'),
                'widget' => 'XLite\View\TestEmail',
            ],
        ];
    }
}
