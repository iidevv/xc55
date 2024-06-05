<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\ItemsList;

use Qualiteam\SkinActQuickbooks\Model\QbcOrderStatus;
use Qualiteam\SkinActQuickbooks\View\Tabs\Quickbooks;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\View\ItemsList\Model\Table;
use XLite\Core\Request;

class QbcOrderStatuses extends Table
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = Quickbooks::TAB_ORDER_STATUSES;

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        
        $list[] = 'modules/Qualiteam/SkinActQuickbooks/settings/order_statuses.css';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function defineColumns()
    {
        return [
            'paymentStatus'     => [
                static::COLUMN_NAME     => static::t('Payment status'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Select\OrderStatus\Payment',
                static::COLUMN_ORDERBY  => 100,
            ],
            'shippingStatus'    => [
                static::COLUMN_NAME     => static::t('Fullfilment status'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Select\OrderStatus\Shipping',
                static::COLUMN_ORDERBY  => 200,
            ],
        ];
    }

    protected function getEntityValue($entity, $name)
    {
        $result = parent::getEntityValue($entity, $name);

        if (empty($result)) {
            $result = '-';
        }

        return $result;
    }

    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::preprocessFieldParams($column, $entity);

        return $result;
    }
    
    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        $paymentStatus = $entity->getPaymentStatus();
        $shippingStatus = $entity->getShippingStatus();

        if (
            !$paymentStatus
            || !$shippingStatus
            || Database::getRepo(QbcOrderStatus::class)->getExistingRecord($paymentStatus, $shippingStatus)
        ) {

            return false;
        }

        return true;
    }
    
    /**
     * Validate data
     *
     * @return boolean
     */
    protected function validateUpdate()
    {
        $validated = true;

        $data = Request::getInstance()->data;

        if ($data) {
            foreach ($data as $id => $v) {
                if (
                    !empty($v['_changed'])
                    && Database::getRepo(QbcOrderStatus::class)
                        ->getExistingRecord($v['paymentStatus'], $v['shippingStatus'], $id)
                ) {
                    $validated = false;
                    $this->errorMessages[] = static::t('Orders to be imported duplicate status error');
                    break;
                }
            }
        }

        return $validated;
    }

    protected function defineRepositoryName()
    {
        return QbcOrderStatus::class;
    }

    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        $data = Database::getRepo(QbcOrderStatus::class)->search($cnd, $countOnly);
        
        return $data;
    }
    
    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return 'q.id';
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
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    protected function getRightActions()
    {
        return [];
    }

    protected function getLeftActions()
    {
        $list   = parent::getLeftActions();
        $list[] = $this->getRemoveActionTemplate();

        return $list;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add condition';
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'quickbooks_order_statuses';
    }
    
    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return \Qualiteam\SkinActQuickbooks\View\StickyPanel\QbcOrderStatuses::class;
    }
}