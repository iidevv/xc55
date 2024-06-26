<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Button;


/**
 * Add address button widget
 */
class AddAddress extends \XLite\View\Button\AddAddress
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = parent::prepareURLParams();

        $params['zero_auth'] = '1';

        return $params;
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return 'add-address popup-button';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/account/add_address_button.twig';
    }
}
