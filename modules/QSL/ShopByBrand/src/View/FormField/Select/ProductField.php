<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\FormField\Select;

/**
 * Selector for mapping a product field.
 */
class ProductField extends \XLite\View\FormField\Select\Regular
{
    /**
     * Cached list of selectable options.
     *
     * @var array
     */
    protected static $options;

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        if (!isset(static::$options)) {
            static::$options = $this->defineDefaultOptions();
        }

        return static::$options;
    }

    /**
     * Define list of options.
     *
     * @return array
     */
    protected function defineDefaultOptions()
    {
        $attributes = [];

        foreach ($this->getProductAttributes() as $attribute) {
            $attributes[$attribute->getId()] = htmlspecialchars($this->getAttributeName($attribute));
        }

        asort($attributes);

        return [0 => static::t('No brand attribute')] + $attributes;
    }

    /**
     * Get text to display as the name of the attribute.
     *
     * @param \XLite\Model\Attribute $attribute Attribute.
     *
     * @return string
     */
    protected function getAttributeName(\XLite\Model\Attribute $attribute)
    {
        return $attribute->getName();
    }

    /**
     * Get list of product attributes.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getProductAttributes()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Attribute')
            ->search($this->getSearchConditions());
    }

    /**
     * Get conditions to search for product attributes.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchConditions()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT}       = null;
        $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS} = null;
        $cnd->{\XLite\Model\Repo\Attribute::SEARCH_TYPE}          = \XLite\Model\Attribute::TYPE_SELECT;

        return $cnd;
    }
}
