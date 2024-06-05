<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\Button;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class PopupLoginLink extends \XC\Reviews\View\Button\PopupLoginLink
{
    protected function prepareURLParams()
    {
        $params = parent::prepareURLParams();

        $params['fromURL'] = str_replace('#product-details-tab-reviews', Module::getYotpoAncoreName(), $params['fromURL']);

        return $params;
    }
}