<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\ItemsList\Model;

/**
 * Parcel's items list
 */
class ParcelItem extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget params
     */
    public const PARAM_PARCEL_ID = 'parcelId';

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
                static::COLUMN_MAIN          => true,
                static::COLUMN_METHOD_SUFFIX => 'Sku',
            ],
            'name'                           => [
                static::COLUMN_ORDERBY       => 200,
                static::COLUMN_NAME          => static::t('Product name'),
            ],
            'amount'                         => [
                static::COLUMN_ORDERBY       => 300,
                static::COLUMN_NAME          => static::t('Qty'),
                static::COLUMN_NO_WRAP       => true,
            ],
            'weight'                         => [
                static::COLUMN_ORDERBY       => 400,
                static::COLUMN_NAME          => static::t('Weight'),
                static::COLUMN_NO_WRAP       => true,
            ],
            'total_weight'                   => [
                static::COLUMN_ORDERBY       => 500,
                static::COLUMN_NAME          => static::t('Total weight'),
                static::COLUMN_NO_WRAP       => true,
            ],
            'move_item'                      => [
                static::COLUMN_ORDERBY       => 600,
                static::COLUMN_NAME          => static::t('Move item'),
                static::COLUMN_NO_WRAP       => true,
                static::COLUMN_TEMPLATE      => 'modules/XC/CanadaPost/shipments/parcel.products.move_item.twig',
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
            static::PARAM_PARCEL_ID => new \XLite\Model\WidgetParam\TypeInt('Parcel ID', 0),
        ];
    }

    /**
     * Return true if widget can be displayed
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getParcel();
    }

    /**
     * Get parcel ID
     *
     * @return integer
     */
    protected function getParcelId()
    {
        return $this->getParam(static::PARAM_PARCEL_ID);
    }

    /**
     * Get products return
     *
     * @return \XC\CanadaPost\Model\ProductsReturn|null
     */
    protected function getParcel()
    {
        return \XLite\Core\Database::getRepo('XC\CanadaPost\Model\Order\Parcel')
            ->find($this->getParcelId());
    }

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'moveItems';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' capost-parcel-items-list';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XC\CanadaPost\Model\Order\Parcel\Item';
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{static::PARAM_PARCEL_ID} = $this->getParcelId();

        return $result;
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getParcelId();
    }

    // {{{ Search

    // }}}

    // {{{ Columns value getters

    /**
     * Get value of the "sku" column
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return string
     */
    protected function getSkuColumnValue(\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getOrderItem()->getSku();
    }

    /**
     * Get value of the "name" column
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return string
     */
    protected function getNameColumnValue(\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getOrderItem()->getName();
    }

    /**
     * Get value of the "amount" column
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return integer
     */
    protected function getAmountColumnValue(\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getAmount();
    }

    /**
     * Get value of the "weight" column
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return float
     */
    protected function getWeightColumnValue(\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getWeightInKg(true);
    }

    /**
     * Get value of the "total_weight" column
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item Parcel item model
     *
     * @return float
     */
    protected function getTotalWeightColumnValue(\XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $item->getTotalWeightInKg(true);
    }

    // }}}

    // {{{ Preprocessors

    /**
     * Pre-process "name" field
     *
     * @param string                                              $name   Product name
     * @param array                                               $column Column data
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item   Parcel item
     *
     * @return string
     */
    protected function preprocessName($name, array $column, \XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        $object = $item->getOrderItem()->getObject();
        if ($object && !$object->isDeleted()) {
            $name = '<a href="' . $item->getOrderItem()->getObject()->getURL() . '">' . $name . '</a>';
        }

        return $name;
    }

    /**
     * Pre-process "weight" field
     *
     * @param string                                              $weight Item's weight
     * @param array                                               $column Column data
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item   Parcel item
     *
     * @return string
     */
    protected function preprocessWeight($weight, array $column, \XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $weight . ' ' . static::t('kg');
    }

    /**
     * Pre-process "total_weight" field
     *
     * @param string                                              $weight Item's weight
     * @param array                                               $column Column data
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $item   Parcel item
     *
     * @return string
     */
    protected function preprocessTotalWeight($weight, array $column, \XC\CanadaPost\Model\Order\Parcel\Item $item)
    {
        return $weight . ' ' . static::t('kg');
    }

    // }}}
}
