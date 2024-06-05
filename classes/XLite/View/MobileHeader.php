<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Mobile header
 *
 * @ListChild (list="layout.header.mobile", weight="100")
 */
class MobileHeader extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/mobile.header.twig';
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
     * Should customer zone have language selector
     *
     * @return boolean
     */
    public function isNeedLanguageDropDown()
    {
        return 1 < \XLite\Core\Database::getRepo('XLite\Model\Language')->countBy(
            [
                'enabled'   => true,
                'added'     => true
            ]
        );
    }
}
