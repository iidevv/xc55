<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\FormField\Select\CheckboxList\ProductSearch;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Repo\Product;

/**
 * @Extender\Mixin
 */
class ByCondition extends \XLite\View\FormField\Select\CheckboxList\ProductSearch\ByCondition
{
    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array_merge(
            parent::getDefaultOptions(),
            [Product::P_BY_TAG => static::t('Tag')]
        );
    }
}
