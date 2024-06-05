<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Page\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * Messages
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Messages extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'messages';

        return $result;
    }

    /**
     * Returns widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/VendorMessages/page/conversations.twig';
    }
}
