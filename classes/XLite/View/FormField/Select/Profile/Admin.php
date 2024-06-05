<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Profile;

/**
 * Admin profiles select
 */
class Admin extends \XLite\View\FormField\Select\Profile\AProfile
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Profile')->findAllAdminAccounts() as $profile) {
            $list[$profile->getProfileId()] = $profile->getLogin();
        }

        return $list;
    }
}
