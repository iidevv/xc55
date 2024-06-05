<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\View\FormField\Select;

/**
 * Signature selector for settings page
 */
class Signature extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'NO_SIGNATURE_REQUIRED'    => 'No signature required',
            'INDIRECT'                 => 'Indirect signature required',
            'DIRECT'                   => 'Direct signature required',
            'ADULT'                    => 'Adult signature required',
        ];
    }
}
