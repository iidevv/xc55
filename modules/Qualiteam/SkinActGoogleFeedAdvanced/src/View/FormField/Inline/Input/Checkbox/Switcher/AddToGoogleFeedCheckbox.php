<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\View\FormField\Inline\Input\Checkbox\Switcher;

class AddToGoogleFeedCheckbox extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    protected function getEntityValue()
    {
        return $this->getEntity()->getAddToGoogleFeed() ?? false;
    }
}
