<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActGraphQLApi\View;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

abstract class ItemsListProductSearch extends \XLite\View\ItemsList\Product\Customer\Search
{
    public function getSortByFieldsForMobileApi()
    {
        return $this->getSortByFields();
    }
}
