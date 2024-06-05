<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\FormField\Select;

use XLite\View\FormField\Select\Regular;

/**
 * Tracking type selector
 */
class TrackingType extends Regular
{
    /**
     * Get default options
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getDefaultOptions()
    {
        /** @noinspection PackedHashtableOptimizationInspection */
        return [
            '1' => static::t('A single domain'),
            '2' => static::t('One domain with multiple subdomains'),
            '3' => static::t('Multiple top-level domains'),
        ];
    }
}
