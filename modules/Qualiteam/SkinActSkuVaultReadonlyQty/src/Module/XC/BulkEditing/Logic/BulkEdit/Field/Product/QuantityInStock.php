<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\BulkEditing")
 */
class QuantityInStock extends \XC\BulkEditing\Logic\BulkEdit\Field\Product\QuantityInStock
{
    public static function getSchema($name, $options)
    {
        $schema = parent::getSchema($name, $options);
        $schema[$name]['is_qty'] = true;

        return $schema;
    }

    public static function populateData($name, $object, $data)
    {
        if ($object->isSkippedFromSync()) {
            parent::populateData($name, $object, $data);
        }
    }
}
