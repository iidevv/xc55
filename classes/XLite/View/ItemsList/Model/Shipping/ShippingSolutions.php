<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

use XLite\View\ItemsList\Model\Shipping\ShippingSolutionsTrait;

/**
 * Shipping solutions
 */
class ShippingSolutions extends \XLite\View\ItemsList\Model\Shipping\Carriers
{
    use ShippingSolutionsTrait;

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'logo'        => [
                static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/shipping_solutions/cell.logo.twig',
                static::COLUMN_ORDERBY  => 100,
            ],
            'generalInfo' => [
                static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/shipping_solutions/cell.generalInfo.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'manage'      => [
                static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/shipping_solutions/cell.manage.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
        ];
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Get right actions templates
     *
     * @return array
     */
    protected function getRightActions()
    {
        return [];
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Shipping\Method::P_PROCESSOR} = 'shipping_solution';
        unset($result->{\XLite\Model\Repo\Shipping\Method::P_ADDED});

        return $result;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $data = parent::getData($cnd, false);

        $enabledShippingSolutions = array_filter($data, function ($shippingSolution) {
            return $this->isEnabled($shippingSolution);
        });

        return $countOnly
            ? count($enabledShippingSolutions)
            : $enabledShippingSolutions;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' shipping-solutions';
    }
}
