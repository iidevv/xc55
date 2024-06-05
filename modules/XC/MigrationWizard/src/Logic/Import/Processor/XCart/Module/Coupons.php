<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Reviews module
 */
class Coupons extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    public const PRODUCT_COUPON_COMMENT = 'Coupon for productId = ';

    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        \XLite\Core\TmpVars::getInstance()->migration_category_processed = false;
    }

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'code' => [
                static::COLUMN_IS_KEY => true,
            ],
            'value' => [],
            'type' => [],
            'productId' => [],
            'categories' => [],
            'totalRangeBegin' => [],
            'dateRangeEnd' => [],
            'enabled' => [],
            'uses' => [],
            'usesLimit' => [],
            'usesLimitPerUser' => [],
            'singleUse' => [],
            'comment' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "dc.coupon AS `code`"
            . ", dc.discount AS `value`"
            . ", dc.coupon_type AS `type`"
            . ", dc.productid AS `productId`"
            . ", CONCAT(dc.categoryid, dc.recursive) AS `categories`"
            . ", dc.minimum AS `totalRangeBegin`"
            . ", IF(dc.per_user = 'N', dc.times, 0) AS `usesLimit`"
            . ", IF(dc.per_user = 'Y', dc.times, 0) AS `usesLimitPerUser`"
            . ", IF(dc.times_used>0, dc.times_used, 0) AS `uses`"
            . ", IF(dc.status = 'A', 1, 0) AS `enabled`"
            . ", 1 AS `singleUse`"
            . ", '' AS `comment`"
            . ", dc.expire AS `dateRangeEnd`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        return "{$prefix}discount_coupons AS dc";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        return $result;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['CDev\Coupons'];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'type' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeTypeValue($value)
    {
        switch ($value) {
            case 'percent':
                $result = '%';
                break;
            case 'absolute':
                $result = '$';
                break;
            case 'free_ship':
                $result = 'S';
                break;
            default:
                $result = '$';
                break;
        }

        return $result;
    }

    /**
     * Normalize 'categories' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeCategoriesValue($value)
    {
        $isRecursive = substr($value, -1) == 'Y';
        $categoryId = intval($value);
        $result = [];

        if (!empty($categoryId)) {
            $result[] = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($categoryId);

            if ($isRecursive) {
                if (\XLite\Core\TmpVars::getInstance()->migration_category_processed == false) {
                    \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();
                    \XLite\Core\TmpVars::getInstance()->migration_category_processed = true;
                }

                $result = array_merge($result, \XLite\Core\Database::getRepo('XLite\Model\Category')->getSubtree($categoryId));
            }
        }
        $result = array_filter($result, static function ($elem) {
            return !is_null($elem);
        });

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        $couponsExist = (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}discount_coupons LIMIT 1"
        );

        $usedCouponsExist = (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}orders WHERE coupon <> '' LIMIT 1"
        );

        return $couponsExist || $usedCouponsExist;
    }

    // }}} </editor-fold>

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating coupons');
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        return \XLite\Core\Database::getRepo('\CDev\Coupons\Model\Coupon')->findOneBy(['code' => $data['code']]);
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        if ($data['productId'] != 0) {
            $data['comment'] = static::PRODUCT_COUPON_COMMENT . $data['productId'];
            $data['enabled'] = 0;
        }

        unset($data['productId']);

        return parent::importData($data);
    }
}
