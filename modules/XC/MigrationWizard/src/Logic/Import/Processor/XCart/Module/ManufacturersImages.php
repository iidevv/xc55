<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Manufacturer Images Processor
 */
class ManufacturersImages extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersValuesAdvancedFields
{
    // {{{ Initialize <editor-fold desc="Initialize" defaultstate="collapsed">
    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        // Make Sure Parent Entities In DB
        static::databaseGetEmFlush();
        parent::initialize();
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
        unset($columns['cleanURLs']);
        unset($columns['cleanURLType']);

        return $columns
            + [
                'xc4EntityId'  => [],
                'image'  => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return parent::defineFieldset()
            . 'mf.manufacturerid xc4EntityId,'
            . 'mf.manufacturerid image';
    }

    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'image',
        ];
    }

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Manufacturer images migrated');
    }

    /**
     * Define Extra Processors.Don'T Call Parent Method To Avoid Nested Overflow
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [];
    }
    protected static function definePreProcessors()
    {
        return [];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'BRAND-IMG-LOAD-FAILED'         => 'Error of image loading. Make sure the "images" directory has write permissions.',
                'BRAND-IMG-FILE-LOAD-FAILED'    => 'Failed to load the file {{value}} because it does not exist',
                'BRAND-IMG-URL-LOAD-FAILED'     => "Couldn't download the image {{value}} from URL",
            ];
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating manufacturers images');
    }

    public static function getImagesCount()
    {
        $prefix = self::getTablePrefix();

        $imagesCount = static::getCellData(
            "SELECT COUNT(*) FROM {$prefix}images_M"
        );

        return $imagesCount;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">
    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Don't Import parent Values
     *
     * @param \QSL\ShopByBrand\Model\Brand $model
     * @param string                $value  Value
     * @param array                 $column Column info
     */
    protected function importNameColumn($model, $value, array $column)
    {
    }
    protected function importXc4EntityIdColumn($model, $value, array $column)
    {
    }
    protected function importOptionColumn($model, $value, array $column)
    {
    }
    protected function importManufactureridColumn($model, $value, array $column)
    {
    }
    protected function importPositionColumn($model, $value, array $column)
    {
    }
    protected function importDescriptionColumn($model, $value, array $column)
    {
    }
    protected function importMetaDescriptionColumn($model, $value, array $column)
    {
    }
    protected function importMetaKeywordsColumn($model, $value, array $column)
    {
    }


    /**
     * Import 'Image' Value
     *
     * @param \QSL\ShopByBrand\Model\Brand $model  Manufacturer
     * @param string                $value  Value
     * @param array                 $column Column info
     */
    protected function importImageColumn($model, $value, array $column)
    {
        if (!$value) {
            return;
        }

        if ($this->verifyValueAsNull($value)) {
            if ($model->getImage()) {
                \XLite\Core\Database::getEM()->remove($model->getImage());
                $model->setImage();
                static::databaseGetEmFlush();
            }
        } elseif ($value = $this->getImageURL('M', $value)) {
            $image = $model->getImage();

            if (!$image) {
                $image = new \QSL\ShopByBrand\Model\Image\Brand\Image();
            }

            $success = $image->loadFromPath($value);

            if ($success) {
                \XLite\Core\Database::getEM()->persist($image);
                $image->setNeedProcess(1);
                $image->setBrand($model);
                $model->setImage($image);
            } else {
                $file = $this->verifyValueAsLocalURL($value) ? $this->getLocalPathFromURL($value) : $value;
                if ($image->getLoadError() === 'unwriteable') {
                    $this->addError('BRAND-IMG-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError() === 'undownloadable') {
                    $this->addWarning('BRAND-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError() === 'nonexistent') {
                    $this->addWarning('BRAND-IMG-FILE-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError()) {
                    $this->addWarning('BRAND-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                }
            }
        }
    }

    // }}} </editor-fold>

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
