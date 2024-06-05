<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class ExtraCouponsAndDiscounts extends \XLite\View\AView
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'extra_coupons_and_discounts';
        return $list;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/body.twig';
    }
}