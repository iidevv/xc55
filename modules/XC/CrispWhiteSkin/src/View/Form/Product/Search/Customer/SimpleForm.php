<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Form\Product\Search\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Simple form
 * @Extender\Mixin
 */
class SimpleForm extends \XLite\View\Form\Product\Search\Customer\SimpleForm
{
    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return array_merge(parent::getDefaultParams(), [
            \XLite\View\ItemsList\Product\Customer\Search::PARAM_INCLUDING => \XLite\Model\Repo\Product::INCLUDING_ALL,
        ]);
    }
}
