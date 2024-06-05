<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Before("CDev\Wholesale")
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return string
     */
    protected function getWholesalePricesUrl()
    {
        $identity = $this->getDataObject()->default->identity;

        return $this->buildURL(
            'product',
            '',
            [
                'product_id' => $identity,
                'page'       => 'wholesale_pricing',
            ]
        );
    }
}
