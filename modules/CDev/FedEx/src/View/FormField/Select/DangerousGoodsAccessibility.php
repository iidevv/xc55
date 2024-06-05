<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\View\FormField\Select;

/**
 * Dangerous goods/accessibility selector for settings page
 */
class DangerousGoodsAccessibility extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''             => '',
            'ACCESSIBLE'   => 'Accessible dangerous goods',
            'INACCESSIBLE' => 'Inaccessible dangerous goods',
        ];
    }
}
