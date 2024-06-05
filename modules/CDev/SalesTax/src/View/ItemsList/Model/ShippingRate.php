<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\ItemsList\Model;

/**
 * ShippingRate items list
 */
class ShippingRate extends \CDev\SalesTax\View\ItemsList\Model\Rate
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if (isset($columns['taxableBase'])) {
            unset($columns['taxableBase']);
        }

        return $columns;
    }

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'shipping' . parent::getDataPrefix();
    }

    /**
     * Get data prefix for new cells
     *
     * @return string
     */
    public function getCreateDataPrefix()
    {
        return 'shipping' . parent::getCreateDataPrefix();
    }

    /**
     * Get data prefix for remove cells
     *
     * @return string
     */
    public function getRemoveDataPrefix()
    {
        return 'shipping' . parent::getRemoveDataPrefix();
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new \XLite\Core\CommonCell();

        $result->{\CDev\SalesTax\Model\Repo\Tax\Rate::PARAM_TAXABLE_BASE}
            = \CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING;

        return $result;
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setTaxableBase(\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING);

        return $entity;
    }

    /**
     * getEmptyListFile
     *
     * @return string
     */
    protected function getEmptyListFile()
    {
        return 'empty_shipping.twig';
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add New Rate';
    }
}
