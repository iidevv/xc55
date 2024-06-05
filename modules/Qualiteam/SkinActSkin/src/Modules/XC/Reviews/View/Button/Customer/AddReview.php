<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\XC\Reviews\View\Button\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Add review button widget
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\Reviews")
 */
class AddReview extends \XC\Reviews\View\Button\Customer\AddReview
{

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' btn-lg regular-main-button');
    }

}
