<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products export step
 * @Extender\Mixin
 */
class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * @inheritdoc
     */
    protected function getWriterTypes()
    {
        $types = parent::getWriterTypes();

        $types['integers'][] = 'stockLevel';
        $types['integers'][] = 'lowLimitLevel';
        $types['integers'][] = 'itemsPerBox';

        $types['floats'][] = 'boxWidth';
        $types['floats'][] = 'boxLength';
        $types['floats'][] = 'boxHeight';
        $types['floats'][] = 'weight';

        $types['currencies'][] = 'price';

        $types['dates'][] = 'arrivalDate';
        $types['dates'][] = 'date';
        $types['dates'][] = 'updateDate';

        return $types;
    }
}
