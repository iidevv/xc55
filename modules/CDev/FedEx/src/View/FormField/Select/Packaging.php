<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\View\FormField\Select;

/**
 * Packaging selector for settings page
 */
class Packaging extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'YOUR_PACKAGING'        => 'My packaging',
            'FEDEX_ENVELOPE'        => 'FedEx Envelope',
            'FEDEX_PAK'             => 'FedEx Pak',
            'FEDEX_BOX'             => 'FedEx Box',
            'FEDEX_TUBE'            => 'FedEx Tube',
            'FEDEX_SMALL_BOX'       => 'FedEx Small Box',
            'FEDEX_MEDIUM_BOX'      => 'FedEx Medium Box',
            'FEDEX_LARGE_BOX'       => 'FedEx Large Box',
            'FEDEX_EXTRA_LARGE_BOX' => 'FedEx Extra Large Box',
            'FEDEX_10KG_BOX'        => 'FedEx 10Kg Box',
            'FEDEX_25KG_BOX'        => 'FedEx 25Kg Box'
        ];
    }
}
