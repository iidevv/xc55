<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\View\FormModel\Product\Attribute;

class HiddenAttribute extends \XC\BulkEditing\View\FormModel\Product\Attribute\AAttribute
{
    public function __construct(array $params)
    {
        $this->scenario = 'product_hidden_attributes';

        parent::__construct($params);
    }
}
