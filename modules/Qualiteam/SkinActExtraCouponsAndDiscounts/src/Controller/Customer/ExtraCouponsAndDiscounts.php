<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Customer;

use Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList\ExtraCouponsAndDiscounts as ExtraCouponsAndDiscountsItemsList;
use Qualiteam\SkinActProMembership\Helpers\Profile;

class ExtraCouponsAndDiscounts extends \XLite\Controller\Customer\ACustomer
{
    public function getItemsListClass()
    {
        return ExtraCouponsAndDiscountsItemsList::class;
    }

    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget_title ?: static::t('My account');
    }

    public function isTitleVisible()
    {
        return true;
    }

    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && (new Profile)->isProfileProMembership();
    }

    protected function addBaseLocation()
    {
    }

}