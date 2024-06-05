<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\ItemsList\Model;

use QSL\ShopByBrand\Model\Repo\Brand as Repo;

class Brand extends \XLite\View\ItemsList\Model\Table
{
    protected $productsCountCache = [];

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/ShopByBrand/brands/style.css';

        return $list;
    }

    /**
     * Returns cached Products count value
     *
     * @param \QSL\ShopByBrand\Model\Brand $brand
     *
     * @return int
     */
    public function getBrandProductsCount(\QSL\ShopByBrand\Model\Brand $brand)
    {
        $brandId = $brand->getId();

        if (!isset($this->productsCountCache[$brandId])) {
            $this->productsCountCache[$brandId] = $brand->getProducts(null, true);
        }

        return $this->productsCountCache[$brandId];
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [

            'image' => [
                static::COLUMN_NAME         => '',
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\FileUploader\Image',
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\EmptyField',
                static::COLUMN_PARAMS       => ['required' => false],
                static::COLUMN_ORDERBY      => 100,
            ],

            'name' => [
                static::COLUMN_NAME    => static::t('Brand'),
                static::COLUMN_LINK    => 'brand',
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_MAIN    => true
            ],

            'products' => [
                static::COLUMN_NAME     => static::t('Products'),
                static::COLUMN_TEMPLATE => 'modules/QSL/ShopByBrand/brands/parts/info.products.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
        ];
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $option = $entity->getOption();

        if ($option) {
            $option->getRepository()->delete($option, false);
        }

        return parent::removeEntity($entity);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\ShopByBrand\Model\Brand';
    }

    /**
     * Creation button position
     *
     * @return int
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('brand');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New brand';
    }

    /**
     * Mark list as removable
     *
     * @return bool
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return int
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' brands';
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\ShopByBrand\View\StickyPanel\ItemsList\Brand';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{Repo::SEARCH_ORDER_BY} = [
            [
                Repo::SORT_BY_ADMIN_DEFINED,
                'ASC',
            ],
            [
                Repo::SORT_BY_BRAND_NAME,
                'ASC',
            ],
        ];

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * Adds switchers to the brands list.
     *
     * @return bool
     */
    protected function isSwitchable()
    {
        return true;
    }
}
