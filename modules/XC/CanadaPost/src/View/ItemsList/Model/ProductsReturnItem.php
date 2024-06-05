<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\ItemsList\Model;

/**
 * Products return items list
 */
class ProductsReturnItem extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget params
     */
    public const PARAM_RETURN_ID = 'returnId';

    /**
     * Hide panel
     *
     * @return null
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Items are non-removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku'                            => [
                static::COLUMN_ORDERBY       => 100,
                static::COLUMN_NAME          => static::t('SKU'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_METHOD_SUFFIX => 'Sku',
            ],
            'image'                          => [
                static::COLUMN_ORDERBY       => 200,
                static::COLUMN_NAME          => '',
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_TEMPLATE      => 'modules/XC/CanadaPost/products_return/item_cells/image.twig',
            ],
            'name'                           => [
                static::COLUMN_ORDERBY       => 300,
                static::COLUMN_NAME          => static::t('Product name'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_MAIN          => true,
            ],
            'ordered_amount'                 => [
                static::COLUMN_ORDERBY       => 400,
                static::COLUMN_NAME          => static::t('Ordered qty'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_METHOD_SUFFIX => 'OrderedAmount',
            ],
            'amount'                         => [
                static::COLUMN_ORDERBY       => 500,
                static::COLUMN_NAME          => static::t('Return qty'),
                static::COLUMN_NO_WRAP       => true,
            ],
        ];
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_RETURN_ID => new \XLite\Model\WidgetParam\TypeInt('Return ID', 0),
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return true if widget can be displayed
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getProductsReturn();
    }

    /**
     * Get products return ID
     *
     * @return integer
     */
    protected function getProductsReturnId()
    {
        return $this->getParam(static::PARAM_RETURN_ID);
    }

    /**
     * Get products return
     *
     * @return \XC\CanadaPost\Model\ProductsReturn|null
     */
    protected function getProductsReturn()
    {
        return \XLite\Core\Database::getRepo('XC\CanadaPost\Model\ProductsReturn')
            ->find($this->getProductsReturnId());
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' capost-return-items-list';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XC\CanadaPost\Model\ProductsReturn\Item';
    }

    // {{{ Search

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XC\CanadaPost\Model\Repo\ProductsReturn\Item::P_RETURN_ID => static::PARAM_RETURN_ID,
        ];
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->{$modelParam} = $this->getParam($requestParam);
        }

        return $result;
    }

    // }}}

    // {{{ Columns value getters

    /**
     * Get value of the "sku" column
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $item Products return item model
     *
     * @return string
     */
    protected function getSkuColumnValue(\XC\CanadaPost\Model\ProductsReturn\Item $item)
    {
        return $item->getOrderItem()->getSku();
    }

    /**
     * Get value of the "name" column
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $item Products return item model
     *
     * @return string
     */
    protected function getNameColumnValue(\XC\CanadaPost\Model\ProductsReturn\Item $item)
    {
        return $item->getOrderItem()->getName();
    }

    /**
     * Get value of the "ordered amount" column
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $item Products return item model
     *
     * @return integer
     */
    protected function getOrderedAmountColumnValue(\XC\CanadaPost\Model\ProductsReturn\Item $item)
    {
        return $item->getOrderItem()->getAmount();
    }

    // }}}

    // {{{ Preprocessors

    /**
     * Pre-process "name" field
     *
     * @param string                                                $name   Product name
     * @param array                                                 $column Column data
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $item   Products return item model
     *
     * @return string
     */
    protected function preprocessName($name, array $column, \XC\CanadaPost\Model\ProductsReturn\Item $item)
    {
        $object = $item->getOrderItem()->getObject();
        if ($object && !$object->isDeleted()) {
            $name = '<a href="' . $item->getOrderItem()->getObject()->getURL() . '">' . $name . '</a>';
        }

        return $name;
    }

    // }}}
}
