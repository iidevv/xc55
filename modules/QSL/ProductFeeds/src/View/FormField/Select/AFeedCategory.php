<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\FormField\Select;

/**
 * Abstract feed category selector.
 */
abstract class AFeedCategory extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    public const PARAM_UNMODIFIED_OPTION = 'unmodifiedOption';

    /**
     * Get repository class for the specific category model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    abstract protected function getRepository();

    /**
     * Check whether "Leave unmodified" option is enabled.
     *
     * @return boolean
     */
    public function isUnmodifiedOptionEnabled()
    {
        return $this->getParam(static::PARAM_UNMODIFIED_OPTION);
    }

    /**
     * Register CSS files.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductFeeds/product/parts/feed_category.css';

        return $list;
    }

    /**
     * Set value.
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if ($value === null) {
            $value = 0;
        }

        parent::setValue($value);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_UNMODIFIED_OPTION => new \XLite\Model\WidgetParam\TypeBool('Unmodified option', false),
        ];
    }

    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [
            0 => static::t('--- Not assigned (feed category) ---')
        ];

        foreach ($this->getFeedCategories() as $category) {
            $list[$category->getId()] = htmlspecialchars($this->shortenCategoryName($category->getName()));
        }

        return $list;
    }

    /**
     * Get the raw list of feed categories.
     *
     * @return array
     */
    protected function getFeedCategories()
    {
        return $this->getRepository()->getAll();
    }

    /**
     * Get array of options for the select list.
     *
     * @return array
     */
    protected function getOptions()
    {
        // We can't do this in getDefaultOptions() as isUnmodifiedOptionsEnabled() is unknown at that moment
        $list = $this->isUnmodifiedOptionEnabled() ? [-1 => static::t('--- Do not modify (feed category) ---')] : [];

        return $list + parent::getOptions();
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
        return $name;
    }

    /**
     * Assemble classes.
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'feed-category-selector';

        return $classes;
    }
}
