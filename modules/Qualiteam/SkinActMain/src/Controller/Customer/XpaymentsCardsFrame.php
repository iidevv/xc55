<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActMain\Controller\Customer;


use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;

class XpaymentsCardsFrame extends \XPay\XPaymentsCloud\Controller\Customer\XpaymentsCards
{
    use \Qualiteam\SkinActGraphQLApi\Controller\Features\GraphqlApiContextTrait;

    public function checkAccess()
    {
        // from trait
        return parent::checkAccess()
            && \XLite\Model\Cart::getInstance()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && \XLite\Core\Auth::getInstance()->getProfile()->getProfileId() === \XLite\Model\Cart::getInstance()->getOrigProfile()->getProfileId();
    }

    public function doNoAction()
    {
        \XLite\Core\Session::getInstance()->cartToken = $this->getCartToken();
    }

    protected function getViewerClass()
    {
        return '\Qualiteam\SkinActMain\View\XpaymentsCardsFrame';
    }

    public function profile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    public function allowZeroAuth()
    {
        return ZeroAuth::getInstance()->allowZeroAuth();
    }
}