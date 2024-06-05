<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Tabs;

class GoogleProductFeed extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();

        $list[] = 'google_shopping_groups';
        $list[] = 'google_feed';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'google_shopping_groups' => [
                'weight' => 100,
                'title'  => static::t('Configuration'),
                'widget' => 'XC\GoogleFeed\View\Admin\GoogleShoppingGroups'
            ],
            'google_feed'            => [
                'weight' => 200,
                'title'  => static::t('Generation'),
                'widget' => 'XC\GoogleFeed\View\Admin\GoogleFeed'
            ]
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
