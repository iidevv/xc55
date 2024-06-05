<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Module\XC\ProductVariants\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class OrderReports extends \QSL\OrderReports\Controller\Admin\OrderReports
{
    /**
     * @return array
     */
    protected function getSchemaColumns()
    {
        $schema = parent::getSchemaColumns();
        if ($this->getPage() === 'product') {
            $schema[] = 'Variant Id';
        }

        return $schema;
    }

    /**
     * @param array $rowData
     *
     * @return array
     */
    protected function getSchemaValues(array $rowData)
    {
        $data = parent::getSchemaValues($rowData);
        if ($this->getPage() === 'product') {
            $data[] = $rowData['variant_id'];
        }

        return $data;
    }
}
