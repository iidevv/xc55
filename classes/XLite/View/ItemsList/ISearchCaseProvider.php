<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

/**
 * ISearchCaseProvider
 */
interface ISearchCaseProvider
{
    /**
     * Get search case
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchCase();

    /**
     * @param \XLite\View\ItemsList\ISearchValuesStorage $storage
     */
    public function setDefaultValuesStorage(ISearchValuesStorage $storage);
}
