<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\View\Form;

/**
 * Location selector
 */
class LocationSelect extends \XLite\View\Form\AForm
{
    /**
     * Get default form target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'location_select';
    }

    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'change_location';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['widget'] = '\XC\Geolocation\View\LocationSelect';

        if (\XLite\Core\Request::getInstance()->returnUrl) {
            $list['returnURL'] = \XLite\Core\Request::getInstance()->returnUrl;
        }

        return $list;
    }
}
