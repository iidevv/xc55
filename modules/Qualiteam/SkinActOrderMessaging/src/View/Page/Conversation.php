<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Conversation
 * @Extender\Mixin
 */
class Conversation extends \XC\VendorMessages\View\Page\Conversation
{

    /**
     * Returns widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return \XLite::isAdminZone()
            ? 'modules/Qualiteam/SkinActOrderMessaging/page/conversation.twig'
            : parent::getDefaultTemplate();
    }
}