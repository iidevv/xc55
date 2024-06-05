<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("CDev\SimpleCMS")
 */
abstract class Settings extends \XLite\View\Model\Settings
{
    /**
     * Logo & Favicon fields
     *
     * @var array
     */
    protected static $logoFaviconFields = ['logo', 'mobileLogo', 'favicon', 'appleIcon', 'mailLogo', 'pdfLogo'];

}
