<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AutomatedMessages extends \XLite\View\Tabs\AutomatedMessages
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'cart_reminders';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['cart_reminders'] = [
            'weight'   => 400,
            'title'    => static::t('Abandoned cart reminders'),
            'template' => 'modules/QSL/AbandonedCartReminder/cart_reminders/list.twig',
        ];

        return $list;
    }
}
