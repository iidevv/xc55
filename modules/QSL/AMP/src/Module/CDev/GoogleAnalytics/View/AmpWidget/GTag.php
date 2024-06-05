<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Module\CDev\GoogleAnalytics\View\AmpWidget;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend("CDev\GoogleAnalytics")
 */
class GTag extends AWidget
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/google_analytics/gtag.twig';
    }
}
