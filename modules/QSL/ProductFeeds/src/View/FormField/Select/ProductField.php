<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\FormField\Select;

/**
 * Selector for mapping a product field.
 */
class ProductField extends \XLite\View\FormField\Select\Multiple
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
        $upcIsbnEnabled = \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'SystemFields');
        $variantsEnabled = \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants');

        $list = [
            'productId' => static::t('Database #'),
            'sku'       => static::t('SKU'),
        ];

        if ($upcIsbnEnabled) {
            $list['upcIsbn'] = static::t('UPC/ISBN');
            $list['mnfVendor'] = static::t('Mnf/Vendor #');
        }

        if ($variantsEnabled) {
            $list['sku'] = static::t('Item SKU');
            $list['familySku'] = static::t('Family SKU');

            if ($upcIsbnEnabled) {
                $list['upcIsbn'] = static::t('Item UPC/ISBN');
                $list['familyUpcIsbn'] = static::t('Family UPC/ISBN');
                $list['mnfVendor'] = static::t('Item Mnf/Vendor #');
                $list['familyMnfVendor'] = static::t('Family Mnf/Vendor #');
            }
        }

        $attributes = [];
        foreach ($this->getProductAttributes() as $attribute) {
            $attributes['attr:' . $attribute->getId()] = htmlspecialchars($this->getAttributeName($attribute));
        }
        asort($attributes);

        return $list + $attributes;
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
        $class = $attribute->getProductClass();

        return $class
            ? static::t('"{{name}}" ({{class}})', ['class' => $class->getName(), 'name' => $attribute->getName()])
            : static::t('"{{name}}"', ['name' => $attribute->getName()]);
    }

    /**
     * Get list of product attributes.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getProductAttributes()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Attribute')
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
        $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
        $cnd->{\XLite\Model\Repo\Attribute::SEARCH_TYPE} = \XLite\Model\Attribute::TYPE_SELECT;

        return $cnd;
    }
}
