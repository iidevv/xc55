<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\Button;

use XCart\Extender\Mapping\ListChild;

/**
 * Facebook button
 *
 * @ListChild (list="buttons.share", weight="100")
 */
class Facebook extends \CDev\GoSocial\View\Button\ASocialButton
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_share_use;
    }

    /**
     * Get button type
     *
     * @return string
     */
    public function getButtonType()
    {
        return self::BUTTON_CLASS_FACEBOOK;
    }

    /**
     * Get button type
     *
     * @return string
     */
    public function getButtonLabel()
    {
        return static::t('Share');
    }
}
