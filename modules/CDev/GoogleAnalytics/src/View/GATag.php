<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View;

use XCart\Extender\Mapping\ListChild;
use CDev\GoogleAnalytics\Core\GA;

/**
 * Tag declaration
 *
 * @ListChild (list="head", zone="customer", weight="0")
 * @ListChild (list="head", zone="admin", weight="0")
 */

class GATag extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/ga_tag.twig';
    }

    protected function displayTag(): string
    {
        return GA::getLibrary()->getTagContent();
    }
}
