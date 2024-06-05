<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\Form\Login\Customer;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AddReviewAuthorizationForm extends \XC\Reviews\View\Form\Login\Customer\AddReviewAuthorizationForm
{
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        $list['referer'] = str_replace('#product-details-tab-reviews', Module::getYotpoAncoreName(), $list['referer']);

        return $list;
    }
}