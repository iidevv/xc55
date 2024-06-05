<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\FormModel\BulkEdit\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend ("XC\BulkEditing")
 */
class Coupons extends \XC\BulkEditing\View\FormModel\Product\AProduct
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/form_model/bulk_edit/product.less';

        return $list;
    }

    public function __construct(array $params)
    {
        $this->scenario = 'coupons';

        parent::__construct($params);
    }
}
