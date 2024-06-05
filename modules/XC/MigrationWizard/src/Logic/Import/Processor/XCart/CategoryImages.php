<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Category images processor
 */
class CategoryImages extends \XLite\Logic\Import\Processor\Categories
{
    // {{{ Initialize <editor-fold desc="Initialize" defaultstate="collapsed">
    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        $this->importer->getOptions()->commonData['needRemoveDuplicateImages'] = true;
    }
    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        return $columns
            + [
                'xc4EntityId'  => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return 'c.categoryid xc4EntityId,'
            . 'c.categoryid categoryId,'
            . 'c.categoryid image,'
            . 'IF(c.parentid = 0, "root", c.parentid) path';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}categories c";
    }

    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'image',
        ];
    }

    // }}} </editor-fold>

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Category images migrated');
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating category images');
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getPathValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT parentid, category'
            . " FROM {$prefix}categories"
            . ' WHERE categoryid = ?'
        );
    }

    public static function getImagesCount()
    {
        $prefix = self::getTablePrefix();

        $imagesCount = static::getCellData(
            "SELECT COUNT(*) FROM {$prefix}images_C"
        );

        return $imagesCount;
    }

    /**
     * Normalize 'image' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeImageValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            return $this->getImageURL('C', $value);
        }, ['normalizeImageValue', $value]);
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Category
     */
    protected function detectModel(array $data)
    {
        $categoryId = $this->normalizeValueAsUinteger($data['categoryId']);

        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find($categoryId);
    }

    /**
     * Import 'categoryId' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     */
    protected function importCategoryIdColumn(\XLite\Model\Category $model, $value, array $column)
    {
    }

    /**
     * Import 'image' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param string                $value  Value
     * @param array                 $column Column info
     */
    protected function importImageColumn(\XLite\Model\Category $model, $value, array $column)
    {
        parent::importImageColumn($model, $this->normalizeImageValue($value), $column);
    }

    /**
     * Import 'path' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function importPathColumn(\XLite\Model\Category $model, $value, array $column)
    {
    }

    /**
     * Return true if import run in update-only mode
     *
     * @return boolean
     */
    protected function isUpdateMode()
    {
        return true;
    }
}
