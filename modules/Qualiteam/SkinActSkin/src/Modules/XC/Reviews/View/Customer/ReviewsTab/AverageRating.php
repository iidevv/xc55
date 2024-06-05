<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\XC\Reviews\View\Customer\ReviewsTab;

use XCart\Extender\Mapping\Extender;

/**
 * Reviews list widget (for tab on product details page)
 * @Extender\Mixin
 * @Extender\Depend("XC\Reviews")
 */
class AverageRating extends \XC\Reviews\View\Customer\ReviewsTab\AverageRating
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/reviews_tab/parts/average_rating_expanded.twig';
    }
}
