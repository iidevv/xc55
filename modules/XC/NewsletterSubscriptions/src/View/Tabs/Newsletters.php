<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Newsletters extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'newsletter_subscribers';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'newsletter_subscribers' => [
                'weight' => 100,
                'title'  => static::t('Subscribers'),
                'widget' => 'XC\NewsletterSubscriptions\View\ItemsList\Subscribers',
            ]
        ];
    }
}
