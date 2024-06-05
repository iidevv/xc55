<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;
use XLite;

/**
 * Link to amp version of the page
 *
 * @ListChild (list="head", weight="3")
 */
class AmpHtmlLink extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/amphtml_link.twig';
    }

    /**
     * Get amp version page url
     *
     * @return boolean
     */
    protected function getAmpPageUrl()
    {
        $controller = XLite::getController();

        $url = XLite::getInstance()->getShopURL(
            $controller->getURL(['amp' => '1'])
        );

        return $url;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return self::hasAMPVersion();
    }
}
