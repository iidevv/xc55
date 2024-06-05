<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Sidebar categories list
 *
 * @ListChild (list="amp.layout.main.center", weight="100")
 */
class BannerRotation extends \XLite\View\BannerRotation\BannerRotation
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/banner_rotation/body.twig';
    }

    /**
     * Amp components
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        return ['amp-carousel'];
    }
}
