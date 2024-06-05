<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\Controller\Customer;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\Controller\Customer\Review
{
    public function getReturnURL()
    {
        return str_replace('#product-details-tab-reviews', Module::getYotpoAncoreName(), parent::getReturnURL());
    }
}