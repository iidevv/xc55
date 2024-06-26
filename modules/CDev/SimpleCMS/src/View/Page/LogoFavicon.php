<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * Logo & favicon page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class LogoFavicon extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['logo_favicon']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'settings/body.twig';
    }
}
