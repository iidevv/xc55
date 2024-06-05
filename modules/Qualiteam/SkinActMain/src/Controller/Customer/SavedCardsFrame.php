<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Controller\Customer;


class SavedCardsFrame extends \Qualiteam\SkinActXPaymentsConnector\Controller\Customer\SavedCards
{

    public function profile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    protected function getViewerClass()
    {
        return '\Qualiteam\SkinActMain\View\SavedCardsFrame';
    }

}