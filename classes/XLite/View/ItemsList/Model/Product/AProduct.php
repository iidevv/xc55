<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product;

/**
 * Abstract product-base list
 */
abstract class AProduct extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_PRICE  = 'p.price';
    public const SORT_BY_MODE_NAME   = 'translations.name';
    public const SORT_BY_MODE_SKU    = 'p.sku';
    public const SORT_BY_MODE_AMOUNT = 'p.amount';


    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Product';
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['products'];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' products';
    }
}
