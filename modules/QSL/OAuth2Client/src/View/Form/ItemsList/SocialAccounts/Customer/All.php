<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Form\ItemsList\SocialAccounts\Customer;

/**
 * Customer social accounts
 */
class All extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'social_accounts';
    }
}
