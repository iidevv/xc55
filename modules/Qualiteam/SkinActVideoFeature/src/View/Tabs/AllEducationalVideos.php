<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class AllEducationalVideos extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'educational_videos',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'educational_videos'    => [
                'weight' => 100,
                'title'  => static::t('SkinActVideoFeature all videos'),
                'widget' => 'Qualiteam\SkinActVideoFeature\View\ItemsList\Model\EducationalVideos',
            ],
        ];
    }
}