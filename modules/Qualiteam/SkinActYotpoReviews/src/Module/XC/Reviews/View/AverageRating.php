<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AverageRating extends \XC\Reviews\View\AverageRating
{
    public function getRatedProductURL()
    {
        return str_replace('#product-details-tab-reviews', Module::getYotpoAncoreName(), parent::getRatedProductURL());
    }
}