<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

class MyMessages extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'notifications';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'notifications' => [
                'weight' => 100,
                'title'  => static::t('Email notifications'),
                'widget' => 'XLite\View\ItemsList\Model\Notification',
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }
}
