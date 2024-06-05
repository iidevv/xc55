<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Product Options module
 */
class ProductOptions extends \XLite\Logic\Import\Processor\Attributes
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const PO_CLASS = '';
    public const PO_GROUP = '';
    public const PO_TYPE = \XLite\Model\Attribute::TYPE_SELECT;

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return parent::defineColumns() + [
            'xc4EntityId' => [],
        ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $class = ProductOptions::PO_CLASS;
        $group = ProductOptions::PO_GROUP;

        $textType = \XLite\Model\Attribute::TYPE_TEXT;
        $defaultType = ProductOptions::PO_TYPE;
        $type = "IF(cs.is_modifier = 'T' or cs.is_modifier = 'A', '{$textType}', '{$defaultType}')";

        $options = static::getProductOptionsSQL();

        return "cs.classid AS `xc4EntityId`"
            . ", cs.class AS `name`"
            . ", cs.orderby AS `position`"
            . ", p.productcode AS `product`"
            . ", '{$class}' AS `class`"
            . ", '{$group}' AS `group`"
            . ", ( {$options} ) AS `options`"
            . ", {$type} AS `type`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        return "{$tp}products AS p"
            . " INNER JOIN {$tp}classes AS cs"
                . " ON cs.`productid` = p.`productid`"
                    . " AND cs.`is_modifier` IN ('Y','T','A')";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result = "p.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get product options SQL
     *
     * @return string
     */
    public static function getProductOptionsSQL()
    {
        $tp = self::getTablePrefix();

        return "SELECT GROUP_CONCAT( DISTINCT option_name ORDER BY co.orderby ASC SEPARATOR '&&' )"
                . " FROM {$tp}class_options AS co"
                . " WHERE co.classid = cs.classid";
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $dataset = self::defineDataset();

        return Configuration::isModuleEnabled(Configuration::MODULE_PRODUCT_OPTIONS)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$dataset} LIMIT 1"
            );
    }

    // }}} </editor-fold>

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product options');
    }

    protected function importProductColumn(\XLite\Model\Attribute $model, $value, array $column)
    {
        $product = $this->normalizeProductValue($value);

        if ($product) {
            $property = null;

            if ($model->isPersistent()) {
                $propQb = \XLite\Core\Database::getRepo('XLite\Model\AttributeProperty')->createQueryBuilder('ap');
                $propQb->andWhere('ap.product = :product')
                    ->andWhere('ap.attribute = :attribute')
                    ->setParameter('product', $product)
                    ->setParameter('attribute', $model);

                $property = $propQb->getSingleResult();
            }

            if (!$property) {
                $property = new \XLite\Model\AttributeProperty();
                $property->setProduct($product);
                $property->setAttribute($model);
                \XLite\Core\Database::getEM()->persist($property);
            }

            $property->setPosition($model->getPosition());

            $model->setProduct($product);
        }
    }
}
