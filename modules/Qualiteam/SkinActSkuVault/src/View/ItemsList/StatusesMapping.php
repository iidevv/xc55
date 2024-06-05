<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\ItemsList;

use Qualiteam\SkinActSkuVault\Model\StatusesMap;
use Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping as Mapping;
use Qualiteam\SkinActSkuVault\View\Tabs\SkuVault;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Order\Status\Shipping;
use XLite\View\ItemsList\Model\Table;

class StatusesMapping extends Table
{
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = SkuVault::TAB_STATUSES_MAPPING;

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVault/statuses_mapping/js/script.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVault/statuses_mapping/css/style.css';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function defineColumns()
    {
        return [
            'xcartPaymentStatus'     => [
                static::COLUMN_NAME         => static::t('X-Cart Payment Status'),
                static::COLUMN_CREATE_CLASS => Mapping\PaymentStatuses::class,
                static::COLUMN_ORDERBY      => 100,
            ],
            'xcartFullfilmentStatus' => [
                static::COLUMN_NAME         => static::t('X-Cart Fullfilment Status'),
                static::COLUMN_CREATE_CLASS => Mapping\FullfilmentStatuses::class,
                static::COLUMN_ORDERBY      => 200,
            ],
            'direction'              => [
                static::COLUMN_NAME         => static::t('Direction'),
                static::COLUMN_CREATE_CLASS => Mapping\Direction::class,
                static::COLUMN_ORDERBY      => 300,
            ],
            'skuvaultCheckoutStatus' => [
                static::COLUMN_NAME         => static::t('SkuVault Checkout Status'),
                static::COLUMN_CREATE_CLASS => Mapping\SkuvaultCheckoutStatuses::class,
                static::COLUMN_ORDERBY      => 400,
            ],
            'skuvaultShippingStatus' => [
                static::COLUMN_NAME         => static::t('SkuVault Shipping Status'),
                static::COLUMN_CREATE_CLASS => Mapping\SkuvaultShippingStatuses::class,
                static::COLUMN_ORDERBY      => 500,
            ],
            'skuvaultSaleState'      => [
                static::COLUMN_NAME         => static::t('SkuVault Sale State'),
                static::COLUMN_CREATE_CLASS => Mapping\SkuvaultSaleState::class,
                static::COLUMN_ORDERBY      => 600,
            ],
            'skuvaultPaymentStatus'  => [
                static::COLUMN_NAME         => static::t('SkuVault Payment Status'),
                static::COLUMN_CREATE_CLASS => Mapping\SkuvaultPaymentStatuses::class,
                static::COLUMN_ORDERBY      => 700,
            ],
            'skuvaultSaleStatus'     => [
                static::COLUMN_NAME         => static::t('SkuVault Sale Status'),
                static::COLUMN_CREATE_CLASS => Mapping\SkuvaultSaleStatuses::class,
                static::COLUMN_ORDERBY      => 800,
                static::COLUMN_PARAMS => [
                    'disabled' => true,
                ],
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

    protected function preprocessXcartPaymentStatus($value)
    {
        $status = Database::getRepo(Payment::class)->findOneBy(['id' => $value]);
        return $status ? $status->getName() : $value;
    }

    protected function preprocessXcartFullfilmentStatus($value)
    {
        $status = Database::getRepo(Shipping::class)->findOneBy(['id' => $value]);
        return $status ? $status->getName() : $value;
    }

    protected function preprocessDirection($value)
    {
        $directions = \Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping\Inline\Direction::OPTIONS;
        return array_key_exists($value, $directions) ? $directions[$value] : $value;
    }

    protected function defineRepositoryName()
    {
        return StatusesMap::class;
    }

    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        $data = Database::getRepo(StatusesMap::class)->search($cnd, $countOnly);
        return $data;
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
        return static::CREATE_INLINE_BOTTOM;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add new';
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'skuvault_statuses_mapping';
    }
}
