<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View\FormField\Select;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class DefaultProductSortOrder extends \XLite\View\FormField\Select\DefaultProductSortOrder
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = [];

        $options = parent::getDefaultOptions();

        $added = false;

        // Insert new option just after the default option
        foreach ($options as $key => $value) {
            $result[$key] = $value;
            if ($key == 'default') {
                $result['newest'] = static::t('Newest first');
                $added = true;
            }
        }

        if (!$added) {
            $result['newest'] = static::t('Newest first');
        }

        return $result;
    }
}
