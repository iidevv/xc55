<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\ItemsList\Product\Customer\Category;

use XCart\Extender\Mapping\ListChild;

/**
 * Category products block
 *
 * @ListChild (list="amp.center.bottom", weight="200")
 */
class AmpMain extends \XLite\View\ItemsList\Product\Customer\Category\Main
{
    /**
     * isDisplayModeSelectorVisible
     *
     * @return boolean
     */
    protected function isDisplayModeSelectorVisible()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getSortByLabel()
    {
        return $this->sortByModes[$this->getSortBy()];
    }

    /**
     * @return string
     */
    protected function getSortOrderLabel($key = null)
    {
        if (!$key || $key === $this->getSortBy()) {
            if (isset($this->sortOrderModes[$this->getSortBy()])) {
                return $this->getSortArrowClassCSS($this->getSortBy()) != ''
                    ? $this->sortOrderModes[$this->getSortBy()][$this->getSortOrder()]
                    : '';
            }

            return $this->getSortArrowClassCSS($this->getSortBy()) != ''
                ? $this->sortOrderModes[$this->getSortOrder()]
                : '';
        }

        return '';
    }

    /**
     * @return bool
     */
    protected function isAmpMainProductList()
    {
        return true;
    }
}
