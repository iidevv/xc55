<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Features;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Model\Cart;

trait GraphqlApiContextTrait
{
    protected function checkStorefrontAccessibility()
    {
        return true;
    }

    public function handleRequest()
    {
        if (!Session::restoreCartFromToken($this->getCartToken())) {
            $this->markAsAccessDenied();
        }

        parent::handleRequest();
    }

    /**
     * @return bool
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && Cart::getInstance()
            && Auth::getInstance()->isLogged()
            && Auth::getInstance()->getProfile()->getProfileId() === Cart::getInstance()->getOrigProfile()->getProfileId();
    }

    /**
     * Mark controller run thread as access denied
     *
     * @return void
     */
    protected function markAsAccessDenied()
    {
        parent::markAsAccessDenied();

        $this->setSuppressOutput(true);
        $this->silent = true;
    }

    /**
     * @return string
     */
    protected function getCartToken()
    {
        return Request::getInstance()->getApiCartToken();
    }
}
