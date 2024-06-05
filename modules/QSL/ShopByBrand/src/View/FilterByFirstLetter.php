<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

use QSL\ShopByBrand\Model\Brand;

class FilterByFirstLetter extends \XLite\View\AView
{
    /**
     * Possible filter chars
     */
    public const FILTER_CHARS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                                 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                                 '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['brands']);
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ShopByBrand/filter/filter_by_first_letter';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return (bool) \XLite\Core\Config::getInstance()->QSL->ShopByBrand->show_filter_by_first_letter;
    }

    /**
     * @return array
     */
    protected function getFilterItems()
    {
        $filterItems = array_fill_keys(static::FILTER_CHARS, 0);

        /** @var \QSL\ShopByBrand\Model\Repo\Brand $brandsRepo */
        $brandsRepo = \XLite\Core\Database::getRepo(Brand::class);

        $conditions = new \XLite\Core\CommonCell();

        if (\XLite\Core\Config::getInstance()->QSL->ShopByBrand->hide_brands_without_products) {
            $conditions->{$brandsRepo::SEARCH_WITH_PRODUCTS} = true;
        }

        foreach ($brandsRepo->search($conditions) as $brand) {
            /** @var \QSL\ShopByBrand\Model\Brand $brand */
            $firstChar = strtoupper($brand->getName()[0]);

            if (isset($filterItems[$firstChar])) {
                ++$filterItems[$firstChar];
            }
        }

        return $filterItems;
    }

    /**
     * @param string|int $char  OPTIONAL
     * @param int        $count OPTIONAL
     */
    protected function getAdditionalItemClass($char = '', $count = 0)
    {
        $char              = (string) $char;
        $class             = ' clickable';
        $activeFirstLetter = $this->getFirstLetter();

        if ($char !== '') {
            if ($count === 0) {
                $class = ' no-items';
            } elseif ($char === $activeFirstLetter) {
                $class = ' active';
            }
        } elseif (
            $activeFirstLetter === null
            && $this->getSubstring() === null
        ) {
            $class = ' active';
        }

        return $class;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
