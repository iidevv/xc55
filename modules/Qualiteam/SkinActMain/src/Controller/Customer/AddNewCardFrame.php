<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Controller\Customer;


use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;


class AddNewCardFrame extends \Qualiteam\SkinActXPaymentsConnector\Controller\Customer\AddNewCard
{

    protected function getViewerClass()
    {
        return '\Qualiteam\SkinActMain\View\AddNewCardFrame';
    }

    public function allowZeroAuth()
    {
        return ZeroAuth::getInstance()->allowZeroAuth();
    }

    public function profile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

}