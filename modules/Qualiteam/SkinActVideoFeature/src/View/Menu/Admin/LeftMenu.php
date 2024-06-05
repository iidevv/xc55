<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("Qualiteam\SkinActProMembership")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['educational_videos'])) {
            $this->relatedTargets['educational_videos'][] = 'educational_video';
        }

        if (!isset($this->relatedTargets['video_categories'])) {
            $this->relatedTargets['video_categories'][] = 'video_category';
            $this->relatedTargets['video_categories'][] = 'category_videos';
        }

        parent::__construct($params);
    }

    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['pro_membership'][self::ITEM_CHILDREN]['video_categories'] = [
            static::ITEM_TITLE      => static::t('SkinActVideoFeature video categories'),
            static::ITEM_TARGET     => 'video_categories',
            static::ITEM_PERMISSION => 'manage catalog',
            static::ITEM_WEIGHT     => 100,
        ];

        $items['pro_membership'][self::ITEM_CHILDREN]['educational_videos'] = [
            static::ITEM_TITLE      => static::t('SkinActVideoFeature educational videos'),
            static::ITEM_TARGET     => 'educational_videos',
            static::ITEM_PERMISSION => 'manage catalog',
            static::ITEM_WEIGHT     => $items['pro_membership'][self::ITEM_CHILDREN]['video_categories'][self::ITEM_WEIGHT] + 100,
        ];

        return $items;
    }
}