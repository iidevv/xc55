<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\View\FormField\Select;

/**
 * COD type selector for settings page
 */
class CODType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'ANY'              => 'Any funds',
            'GUARANTEED_FUNDS' => 'Guaranteed funds',
            'CASH'             => 'Cash',
        ];
    }
}
