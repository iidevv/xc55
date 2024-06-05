<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\BuyProMembership;

use XLite\Core\Request;

class BuyProMembershipForm extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'buy_pro_membership';
    }

    protected function getDefaultAction()
    {
        return 'send_message';
    }

    protected function getFormParams()
    {
        $params = parent::getFormParams();
        $params['isOpeningPopup'] = 1;
        $params['profile_id'] = Request::getInstance()->profile_id;
        return $params;
    }
}