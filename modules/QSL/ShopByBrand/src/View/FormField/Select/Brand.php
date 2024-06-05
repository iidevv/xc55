<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\FormField\Select;

use QSL\ShopByBrand\Model\Repo\Brand as Repo;

/**
 * Brand selector.
 */
class Brand extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/ShopByBrand/form_field/select/brand.css';

        return $list;
    }

    /**
     * Return available reminders.
     *
     * @return array
     */
    protected function getBrandList()
    {
        $list = [
            null => static::t('Any brand'),
        ];

        $brands = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
            ->search($this->getBrandSearchConditions());

        foreach ($brands as $brand) {
            $list[$brand->getBrandId()] = $brand->getName();
        }

        return $list;
    }

    /**
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return bool
     */
    protected function isOptionSelected($value)
    {
        return $value && parent::isOptionSelected($value);
    }

    /**
     * Prepares the search condition for retrieving reminder templates.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getBrandSearchConditions()
    {
        return new \XLite\Core\CommonCell(
            [
                Repo::SEARCH_ORDER_BY      => [Repo::SORT_BY_BRAND_NAME, 'ASC'],
                Repo::SEARCH_WITH_PRODUCTS => true,
            ]
        );
    }

    /**
     * Return default options for the selector.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getBrandList();
    }
}
