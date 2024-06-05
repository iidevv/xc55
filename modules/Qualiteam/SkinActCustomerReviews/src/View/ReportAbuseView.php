<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="center", zone="customer", weight="100")
 */
class ReportAbuseView extends \XLite\View\AView
{

    public static function getAllowedTargets()
    {
        return ['report_abuse'];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/ReportAbuseView.twig';
    }
}