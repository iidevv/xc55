<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Categories selector
 */
class Categories extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Parameters
     */
    public const INDENT_STRING     = '-';
    public const INDENT_MULTIPLIER = 3;

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategoriesPlainList() as $category) {
            $list[$category['category_id']] = $this->getIndentationString($category) . $this->getCategoryName($category);
        }

        return $list;
    }

    /**
     * Return indentation string for displaying category depth level
     *
     * @param array $category Category data
     *
     * @return string
     */
    protected function getIndentationString(array $category)
    {
        return str_repeat(static::INDENT_STRING, ($category['depth'] > 0 ? $category['depth'] : 0) * static::INDENT_MULTIPLIER);
    }

    /**
     * Return translated category name
     *
     * @param array $category Category data
     *
     * @return string
     */
    protected function getCategoryName(array $category)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->find($category['category_id'])->getName();
    }
}
