<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\FormField\Select;

/**
 * Google Shopping category selector
 */
class GoogleShoppingCategory extends \QSL\ProductFeeds\View\FormField\Select\Select2\AFeedCategory
{
    /**
     * Get repository class for the Google Shopping category model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('\QSL\ProductFeeds\Model\GoogleShoppingCategory');
    }

    /**
     * Shorten the category name.
     *
     * @param string $name Original category name.
     *
     * @return string
     */
    protected function shortenCategoryName($name)
    {
        $name = parent::shortenCategoryName($name);

        return ($this->isShortenCategoryNames() && (strlen($name) > 120))
            ? preg_replace('/ ([^>]{16})[^>]+>/', ' \\1... > ', $name)
            : $name;
    }

    /**
     * Whether to shorten feed category names, or not.
     *
     * @return boolean
     */
    protected function isShortenCategoryNames()
    {
        return false;
    }

    /**
     * Get array of options for the select list.
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        foreach ($list as $id => $category) {
            if (is_numeric(strpos($category, '[DEPRECATED!]')) && !$this->isOptionSelected($id)) {
                unset($list[$id]);
            }
        }

        return $list;
    }
}
