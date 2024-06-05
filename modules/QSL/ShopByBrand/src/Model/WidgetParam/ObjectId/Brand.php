<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\WidgetParam\ObjectId;

/**
 * "Brand ID" widget param.
 */
class Brand extends \XLite\Model\WidgetParam\TypeObjectId
{
    /**
     * Return object class name
     *
     * @return string
     */
    protected function getClassName()
    {
        return 'QSL\ShopByBrand\Model\Brand';
    }
}
