<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\FormField\Select;

use CDev\GoogleAnalytics\Core\GA;
use XLite\View\FormField\Select\Regular;

/**
 * Code version selector
 */
class CodeVersion extends Regular
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
        return [
            GA::CODE_VERSION_4 => static::t('GA4'),
            GA::CODE_VERSION_U => static::t('Universal Analytics'),
        ];
    }
}
