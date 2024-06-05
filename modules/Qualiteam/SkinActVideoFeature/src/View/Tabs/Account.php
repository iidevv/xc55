<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Tabs;

use Qualiteam\SkinActVideoFeature\Helpers\Profile;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'educational_videos';

        return $list;
    }
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if (Profile::isProMembership()) {
            $tabs['educational_videos'] = [
                'title'    => static::t('SkinActVideoFeature educational videos'),
                'template' => 'modules/Qualiteam/SkinActVideoFeature/page/educational_videos.twig',
                'weight'   => 20000,
            ];
        }

        return $tabs;
    }

    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'modules/Qualiteam/SkinActVideoFeature/extra_classes.js';

        return $list;
    }
}