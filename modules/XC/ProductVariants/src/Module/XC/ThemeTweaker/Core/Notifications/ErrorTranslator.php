<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\XC\ThemeTweaker\Core\Notifications;

use XCart\Extender\Mapping\Extender;

/**
 * ErrorTranslator
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class ErrorTranslator extends \XC\ThemeTweaker\Core\Notifications\ErrorTranslator
{
    protected static function getErrors()
    {
        return parent::getErrors() + [
                'product_variant' => [
                    'variant_nf' => 'Product variant #{{value}} not found',
                ],
            ];
    }

    protected static function getAvailabilityErrors()
    {
        return parent::getAvailabilityErrors() + [
                'product_variant' => 'No product variants available. Please create at least one.',
            ];
    }
}
