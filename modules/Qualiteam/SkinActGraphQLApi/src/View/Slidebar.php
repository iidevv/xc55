<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Slidebar extends \XLite\View\Slidebar
{
    public static function getDisallowedTargets()
    {
        return array_merge(parent::getDisallowedTargets(), array('graphql_api_checkout'));
    }
}
