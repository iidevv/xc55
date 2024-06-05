<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to user profile section
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    /**
     * @inheritdoc
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        $tabs['messages']['template'] = 'modules/Qualiteam/SkinActOrderMessaging/page/conversations.twig';

        return $tabs;
    }
}