<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\ItemsList\SocialAccounts\Customer;

/**
 * All customer messages
 */
class All extends \QSL\OAuth2Client\View\ItemsList\SocialAccounts\ASocialAccounts
{
    /**
     * @inheritdoc
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }
}
