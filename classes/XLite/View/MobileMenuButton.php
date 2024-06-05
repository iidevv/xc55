<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Mobile menu button
 */
class MobileMenuButton extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/mobile_header_parts/button.twig';
    }

    /**
     * Check block visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getTarget() != 'checkout'
            || $this->isCheckoutAvailable();
    }

    /**
     * Get the list of mobile menu button CSS classes.
     */
    public function getClass(): string
    {
        return 'dropdown mobile_header-slidebar';
    }
}
