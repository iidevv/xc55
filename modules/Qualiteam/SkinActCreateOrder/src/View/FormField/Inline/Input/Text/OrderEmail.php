<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\FormField\Inline\Input\Text;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderEmail extends \XLite\View\FormField\Inline\Input\Text\OrderEmail
{
    protected function defineFieldClass()
    {
        if ($this->getOrder()->getManuallyCreated() && !$this->getOrder()->getOrigProfile()) {
            return '\Qualiteam\SkinActCreateOrder\View\FormField\Select\Model\ProfileSelector';
        }

        return parent::defineFieldClass();
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/order_email_field.js';
        return $list;
    }
}