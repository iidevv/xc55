<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActFixes\Controller\Customer;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector"})
 */
class SavedCards extends \Qualiteam\SkinActXPaymentsConnector\Controller\Customer\SavedCards
{
    public function isTitleVisible()
    {
        return true;
    }

    public function getTitle()
    {
        return static::t('My account');
    }

    protected function addBaseLocation()
    {
        $this->locationPath[] = new \XLite\View\Location\Node\Home();
    }

}