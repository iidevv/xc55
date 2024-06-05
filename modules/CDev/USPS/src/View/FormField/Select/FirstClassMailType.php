<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\View\FormField\Select;

/**
 * First class mail type selector for settings page
 */
class FirstClassMailType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'LETTER'          => 'Letter',
            'LARGE ENVELOPE'  => 'Large Envelope',
            'PARCEL'          => 'Parcel',
            'POSTCARD'        => 'Postcard',
            'LARGE POSTCARD'  => 'Large Postcard',
            'PACKAGE SERVICE' => 'Package service',
        ];
    }
}
