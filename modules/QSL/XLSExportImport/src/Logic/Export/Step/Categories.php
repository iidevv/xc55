<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Categories export step
 * @Extender\Mixin
 */
class Categories extends \XLite\Logic\Export\Step\Categories
{
    /**
     * @inheritdoc
     */
    protected function getWriterTypes()
    {
        $types = parent::getWriterTypes();

        $types['integers'][] = 'position';

        return $types;
    }
}
