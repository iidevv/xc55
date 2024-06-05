<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

use XLite\View\ItemsList\Model\Shipping\CarriersTrait;

/**
 * Shipping carriers list
 */
class Carriers extends \XLite\View\ItemsList\Model\Table
{
    use CarriersTrait;

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'shipping_methods';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'shipping_methods';

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Shipping\Method';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'generalInfo'    => [
                static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/cell.generalInfo.twig',
                static::COLUMN_ORDERBY  => 100,
            ],
            'additionalInfo' => [
                static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/cell.additionalInfo.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'testMode'       => [
                static::COLUMN_TEMPLATE => 'items_list/model/table/shipping/carriers/cell.testModeInfo.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
            'enabled'        => [
                static::COLUMN_CLASS   => \XLite\View\FormField\Inline\Input\Checkbox\Switcher\ShippingMethodEnabled::class,
                static::COLUMN_ORDERBY => 400,
            ],
        ];

        return $columns;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['shipping-carrier'];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' shipping-carriers';
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        return parent::isAllowEntitySwitch($entity)
            && ($entity->getProcessorObject() === null || $entity->getProcessorObject()->isConfigured());
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowTestModeTooltip(\XLite\Model\AEntity $entity)
    {
        return false;
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowHandlingFee(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isShowTaxClass(\XLite\Model\AEntity $entity)
    {
        return $this->isOffline($entity);
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        return [];
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $entity->setAdded(false);

        return true;
    }

    protected function doDisplay($template = null)
    {
        $this->clearSavedData();
        parent::doDisplay($template);
    }

    // {{{ Search

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result                                                  = parent::getSearchCondition();
        $result->{\XLite\Model\Repo\Shipping\Method::P_CARRIER}  = '';
        $result->{\XLite\Model\Repo\Shipping\Method::P_ADDED}    = true;
        $result->{\XLite\Model\Repo\Shipping\Method::P_ORDER_BY} = ['m.position', 'ASC'];

        return $result;
    }

    // }}}
}
