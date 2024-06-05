<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\FormField\Label;

/**
 * Warning for like button in module settings
 */
class LikeSettingsWarning extends \XLite\View\FormField\Label\ALabel
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getFieldTemplate();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/GoSocial/like_warning.twig';
    }
}
