<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCreateOrder\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Model extends \XLite\View\Order\Details\Admin\Model
{
    protected function defineLogin()
    {
        return parent::defineLogin();

        if ($this->getOrder()->getManuallyCreated()) {
            return $this->getWidget(
                [
                    \XLite\View\FormField\Inline\AInline::PARAM_ENTITY => $this->getOrder()->getProfile(),
                    \XLite\View\FormField\Inline\AInline::PARAM_FIELD_NAME => 'login',
                    \XLite\View\FormField\Inline\AInline::FIELD_NAME => 'login',
                    \XLite\View\FormField\Inline\AInline::PARAM_FIELD_NAMESPACE => 'login',
                    \XLite\View\FormField\Inline\AInline::PARAM_VIEW_ONLY => !$this->isOrderEditable(),
                ],
                '\XLite\View\FormField\Select\Model\ProfileSelector'
            );
        }

        return parent::defineLogin();

    }

}