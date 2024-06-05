<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\QSL\CustomerSatisfaction\Core;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Mailer;

/**
 * @Extender\Depend ("QSL\CustomerSatisfaction")
 * @Extender\After ("QSL\CustomerSatisfaction")
 */
class Mail extends Mailer
{
    protected static function buildProductReviewUrl($productId)
    {
        return str_replace('#product-details-tab-reviews', Module::getYotpoAncoreName(), parent::buildProductReviewUrl($productId));
    }
}