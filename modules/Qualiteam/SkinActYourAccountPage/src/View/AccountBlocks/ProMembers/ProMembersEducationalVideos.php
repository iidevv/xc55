<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\ProMembers;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActVideoFeature")
 */
class ProMembersEducationalVideos extends ProMembers
{
    /**
     * Get pro members educational videos url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('educational_videos'),
                    'text' => static::t('SkinActYourAccountPage educational videos'),
                    'is_count' => false,
                    'position' => 1
                ]
            ]
        );
    }
}