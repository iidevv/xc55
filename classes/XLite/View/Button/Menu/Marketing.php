<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Menu;

/**
 * Extensions action
 */
class Marketing extends \XLite\View\Button\APopupLink
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => '',
            'widget' => '',
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'left_menu/marketing/body.twig';
    }

    /**
     * @return bool
     */
    protected function isCacheAvailable()
    {
        return true;
    }
}
