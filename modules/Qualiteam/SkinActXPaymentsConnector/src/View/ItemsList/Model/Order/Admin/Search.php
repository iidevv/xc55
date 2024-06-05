<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\ItemsList\Model\Order\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Model\AEntity;

/**
 * Search order
 *
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    const SORT_BY_MODE_FRAUD =  'p.fraud';
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
		$columns = parent::defineColumns();
		
		$columns['fraud_status_xpc'] = array(
            static::COLUMN_NAME     => '',
            static::COLUMN_SORT     => static::SORT_BY_MODE_FRAUD,
            static::COLUMN_LINK     => 'order',
            static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsConnector/order/fraud_status/status.twig',
			static::COLUMN_ORDERBY  => 350,
		);

		return $columns;
	}

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/order/style.css';

        return $list;
    }

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

        $result .= '#' . $entity->getFraudInfoXpcAnchor();

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
        return $entity->getFraudStatusXpc();
    }
}
