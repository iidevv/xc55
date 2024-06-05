<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

/**
 * Carrier accounts
 */
class CarrierAccounts extends \XLite\View\ItemsList\Model\Shipping\Carriers
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['logo'] = [
            static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/cell.logo.twig',
            static::COLUMN_ORDERBY  => 50,
        ];

        $columns['settings'] = [
            static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/cell.settings.twig',
            static::COLUMN_ORDERBY  => 400,
        ];

        return $columns;
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
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Shipping\Method::P_EXCL_PROCESSORS} = [
            'offline',
            'shipping_solution',
        ];

        return $result;
    }

    /**
     * Get page data
     *
     * @return array
     */
    protected function getPageData()
    {
        return array_filter(parent::getPageData(), static function ($item) {
            /** @var \XLite\Model\Shipping\Method $item */
            return (bool) $item->getProcessorObject();
        });
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowTestModeTooltip(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Processor\AProcessor $processor */
        $processor = $entity->getProcessorObject();

        return $processor
            && $processor->isConfigured()
            && $processor->isTestMode();
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowTaxClass(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCrossIcon()
    {
        return true;
    }
}
