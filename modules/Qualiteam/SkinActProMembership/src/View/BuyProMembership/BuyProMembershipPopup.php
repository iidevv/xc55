<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\BuyProMembership;

class BuyProMembershipPopup extends \XLite\View\AView
{

    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'buy_pro_membership';

        return $result;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProMembership/buy_pro_membership.twig';
    }
}