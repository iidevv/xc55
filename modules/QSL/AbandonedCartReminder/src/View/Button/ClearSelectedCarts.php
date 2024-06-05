<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Button;

/**
 * Enable selected button
 */
class ClearSelectedCarts extends \XLite\View\Button\Regular
{
    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Clear selected carts';
    }

    protected function getClass(): string
    {
        return parent::getClass() . ' hide-if-empty-list';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'clear';
    }
}
