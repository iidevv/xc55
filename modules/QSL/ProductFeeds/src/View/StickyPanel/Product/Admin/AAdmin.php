<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\StickyPanel\Product\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Items list form button
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\StickyPanel\Product\Admin\AAdmin
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list['updateFeedCategories'] = [
            'class' => 'QSL\ProductFeeds\View\Button\FeedCategoriesButton',
            'params' => [
                'label'         => '',
                'attributes'    => [
                    'title' => static::t('Update feed categories')
                ],
                'style'         => 'more-action icon-only hide-on-disable hidden',
                'icon-style'    => 'fa fa-folder-open-o',
                'dropDirection' => 'dropup',
            ],
            'position' => 100,
        ];

        return $list;
    }
}
