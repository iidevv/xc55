<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\FormField\Select;

use Includes\Utils\Module\Manager;
use XLite\Core\Database;
use XLite\Model\Attribute;
use XLite\Model\Repo\Attribute as AttributeRepo;

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
        $upcIsbnEnabled = Manager::getRegistry()->isModuleEnabled('XC', 'SystemFields');

        $list = [
            'productId' => static::t('Database #'),
            'sku'       => static::t('SKU'),
        ];

        if ($upcIsbnEnabled) {
            $list['upcIsbn']   = static::t('UPC/ISBN');
            $list['mnfVendor'] = static::t('Mnf/Vendor #');
        }

        $attributes = [];
        foreach ($this->getProductAttributes() as $attribute) {
            $attributes['attr:' . $attribute->getId()] = htmlspecialchars($this->getAttributeName($attribute));
        }
        asort($attributes);

        return $list + $attributes;
    }

    /**
     * Get list of product attributes.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getProductAttributes()
    {
        return Database::getRepo(Attribute::class)
            ->search($this->getSearchConditions());
    }

    /**
     * Get conditions to search for product attributes.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchConditions()
    {
        $cnd                                  = new \XLite\Core\CommonCell();
        $cnd->{AttributeRepo::SEARCH_PRODUCT} = null;
        $cnd->{AttributeRepo::SEARCH_TYPE}    = Attribute::TYPE_SELECT;

        return $cnd;
    }

    /**
     * Get text to display as the name of the attribute.
     *
     * @param Attribute $attribute Attribute.
     *
     * @return string
     */
    protected function getAttributeName(Attribute $attribute)
    {
        $class = $attribute->getProductClass();

        return $class
            ? static::t('"{{name}}" ({{class}})', ['class' => $class->getName(), 'name' => $attribute->getName()])
            : static::t('"{{name}}"', ['name' => $attribute->getName()]);
    }
}
