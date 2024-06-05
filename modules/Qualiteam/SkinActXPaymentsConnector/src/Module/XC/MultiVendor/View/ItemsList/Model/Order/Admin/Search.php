<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\MultiVendor\View\ItemsList\Model\Order\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Model\AEntity;

/**
 * Search order
 *
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector","XC\MultiVendor"})
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    /**
     * Get column value
     *
     * @param array                $column Column
     * @param AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnClass(array $column, AEntity $entity = null)
    {
        $entity = $this->getParentOrChildEntity($entity);

        $result = parent::getColumnClass($column, $entity);
       
        if ('fraud_status_xpc' == $column[static::COLUMN_CODE]) {
            $result = 'fraud-status-' . $entity->getFraudStatusXpc();
        }

        return $result;
    }

    /**
     * Build entity page URL
     *
     * @param AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function getFraudInfoXpcLink(AEntity $entity)
    {
        $result = Converter::buildURL(
            'order',
            '',
            array('order_number' => $entity->getOrderNumber())
        );

        $result .= '#' . $this->getParentOrChildEntity($entity)->getFraudInfoXpcAnchor();

        return $result;
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param AEntity $entity Model
     *
     * @return mixed
     */
    protected function getFraudInfoXpcTitle(AEntity $entity)
    {
        return $this->getParentOrChildEntity($entity)->getFraudStatusXpc();
    }

    /**
     * Return parent entity if it has parent, or entity itself otherwise
     *
     * @param AEntity $entity
     *
     * @return AEntity
     */
    protected function getParentOrChildEntity(AEntity $entity)
    {
        if ($entity->isChild()) {
            $entity = $entity->getParent();
        }

        return $entity;
    }
}
