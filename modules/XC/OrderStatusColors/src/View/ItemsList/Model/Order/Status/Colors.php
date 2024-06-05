<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\View\ItemsList\Model\Order\Status;

use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Model\AEntity;
use XLite\View\ItemsList\Model\Table;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Order\Status\Shipping;
use XLite\View\FormField\Inline\Label;
use XC\OrderStatusColors\Model\OrderStatusColor;

/**
 * Order status colors items list
 */
class Colors extends Table
{
    /**
     * shipping status columns of table
     *
     * @var array
     */
    protected $tableColumns;

    /**
     * shipping statuses
     *
     * @var array
     */
    protected $shippingStatuses;

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return Payment::class;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return bool
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'order_statuses';
    }

    /**
     * Get wrapper form action
     *
     * @return string
     */
    protected function getFormAction()
    {
        return 'updateColors';
    }

    protected function getPage()
    {
        return 'order_status_colors';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();
        $params['page'] = $this->getPage();

        return $params;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        if (!isset($this->tableColumns)) {
            $this->tableColumns = [
                'customer_name' => [
                    static::COLUMN_NAME => '',
                    static::COLUMN_CLASS => Label::class,
                    static::COLUMN_ORDERBY => 100,
                ],
            ];

            $statuses = Database::getRepo(Shipping::class)
                ->search();

            $index = 100;
            foreach ($statuses as $status) {
                $index += 100;
                $this->tableColumns['status_' . $status->getId()] = [
                    'statusId' => $status->getId(),
                    'statusCode' => $status->getCode(),
                    static::COLUMN_NAME => $status->getCustomerName(),
                    static::COLUMN_TEMPLATE => 'modules/XC/OrderStatusColors/color_selector.twig',
                    static::COLUMN_ORDERBY => $index,
                ];
                $this->shippingStatuses[$status->getId()] = $status;
            }
        }

        return $this->tableColumns;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/OrderStatusColors/style.css';

        return $list;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' status-colors';
    }

    /**
     * Get main column
     *
     * @return array
     */
    protected function getMainColumn()
    {
        return null;
    }

    /**
     * Return shipping status
     *
     * @param integer $id
     *
     * @return Shipping
     */
    protected function getSippingStatus($id)
    {
        if (!isset($this->shippingStatuses[$id])) {
            $this->shippingStatuses[$id] = Database::getRepo(Shipping::class)
                ->find($id);
        }

        return $this->shippingStatuses[$id];
    }

    /**
     * Return color by payment and shipping status
     *
     * @param array $column Column
     * @param AEntity $entity Model
     *
     * @return string
     */
    protected function getColorValue(array $column, AEntity $entity)
    {
        $color = Database::getRepo(OrderStatusColor::class)
            ->getColorByStatuses($entity, $this->getSippingStatus($column['statusId']));

        return strtoupper($color);
    }

    /**
     * Return fieldname by payment and shipping status
     *
     * @param array $column Column
     * @param AEntity $entity Model
     *
     * @return string
     */
    protected function getFieldName(array $column, AEntity $entity)
    {
        return sprintf('colors[%s][%s]', $entity->getId(), $column['statusId']);
    }

    /**
     * Return available colors from configuration
     *
     * @return string
     */
    protected function getAvailableColors()
    {
        return Config::getInstance()->XC->OrderStatusColors->predefined_colors;
    }
}
