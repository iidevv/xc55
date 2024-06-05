<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\FormField\Input\Text;

use XLite\View\FormField\Input\Text\URL;

/**
 * URL (with IPv6 host) 
 *
 */
class URLipv6 extends URL
{
    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $key = array_search('custom[url]', $rules);

        if (false !== $key) {
            unset($rules[$key]);
        }

        $rules[] = 'funcCall[checkURLipv6]';

        return $rules;
    }
}
