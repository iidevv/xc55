<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Logic\Feed\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products step
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\SystemFields")
 */
class ProductsUpcIsbn extends \XC\GoogleFeed\Logic\Feed\Step\Products
{
    /**
     * @param $model
     * @return string
     */
    protected function getMpn($model)
    {
        $result = parent::getMpn($model);

        if (!$result && $model->getMnfVendor()) {
            $result = $model->getMnfVendor();
        }

        return $result;
    }

    /**
     * @param $model
     * @return string
     */
    protected function getGtin($model)
    {
        $result = parent::getGtin($model);

        if (!$result && $model->getUpcIsbn()) {
            $result = $model->getUpcIsbn();
        }

        return $result;
    }
}
