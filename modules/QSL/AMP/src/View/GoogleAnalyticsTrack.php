<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use CDev\GoogleAnalytics\Core\GA;
use XCart\Extender\Mapping\ListChild;

/**
 * AMP analytics GA tracking code
 *
 * @ListChild (list="amp.body", weight="5")
 */
class GoogleAnalyticsTrack extends \XLite\View\AView
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return GA::getResource()->isConfigured();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/google_analytics.twig';
    }

    protected function displayAmpWidget()
    {
        return GA::getLibrary()->getAmpWidgetContent();
    }

    /**
     * Amp components
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        return ['amp-analytics'];
    }
}
