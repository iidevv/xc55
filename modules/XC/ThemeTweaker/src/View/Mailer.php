<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;

/**
 * Theme tweaker template page view
 * @Extender\Mixin
 */
class Mailer extends \XLite\View\Mailer
{
    public function getNotificationEditableContent($zone)
    {
        return $this->compile('modules/XC/ThemeTweaker/common/layout.twig', $zone, true);
    }

    public function getNotificationPreviewContent($zone)
    {
        return $this->compile($this->get('layoutTemplate'), $zone, true);
    }
}
