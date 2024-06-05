<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Info extends \XLite\View\FormModel\Product\Info
{
    public function getTooltipFields()
    {
        return $this->prepareFields($this->defineFields());
    }
}