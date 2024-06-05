<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Module\XC\ProductTags\View\FormField\Inline\Input;

use XCart\Extender\Mapping\Extender;

/**
 * Class Text
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class Text extends \XC\ProductTags\View\FormField\Inline\Input\Text
{
    protected function isEditable()
    {
        return parent::isEditable() && ($this->getEditOnly() || !\XLite\Core\Auth::getInstance()->isVendor());
    }
}
