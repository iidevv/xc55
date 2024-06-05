<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Logic\Import;

use XCart\Extender\Mapping\Extender;

/**
 * Importer
 * @Extender\Mixin
 */
class Importer extends \XLite\Logic\Import\Importer
{
    /**
     * Get processor list
     *
     * @return array
     */
    public static function getProcessorList()
    {
        return array_merge(
            parent::getProcessorList(),
            [
                'XC\Reviews\Logic\Import\Processor\Reviews',
            ]
        );
    }
}
