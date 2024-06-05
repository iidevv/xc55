<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Form\ItemsList\SocialAccounts\Admin;

/**
 * Customer social accounts
 */
class Profile extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'social_accounts';
    }

    /**
     * @inheritdoc
     */
    protected function getCommonFormParams()
    {
        return parent::getCommonFormParams()
            + [
                'profile_id' => \XLite\Core\Request::getInstance()->profile_id,
            ];
    }
}
