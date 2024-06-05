<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\FormField\Input;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Rating extends \XC\Reviews\View\FormField\Input\Rating
{
    protected function isEditable()
    {
        return false;
    }
}