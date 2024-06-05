<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Core\FreeShipping;

use XCart\Extender\Mapping\Extender;

/**
 * Class to collect labels for displaying in items list
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\FreeShipping")
 */
class Labels extends \XC\FreeShipping\Core\Labels
{
    /**
     * Get content of Free shipping label
     *
     * @return array
     */
    protected static function getLabelContent()
    {
        return [
            'blue free-shipping' => \XLite\Core\Translation::getInstance()->translate('Free shipping'),
        ];
    }
}
