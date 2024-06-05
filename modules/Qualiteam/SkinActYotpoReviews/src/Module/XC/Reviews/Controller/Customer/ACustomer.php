<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("XC\Reviews")
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    public function isReplaceAddReviewWithLogin()
    {
        return false;
    }
}