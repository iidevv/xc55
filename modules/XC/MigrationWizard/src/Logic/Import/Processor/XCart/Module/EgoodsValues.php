<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * E-Goods module
 */
class EgoodsValues extends \XLite\Logic\Import\Processor\Products
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['attachmentProvider'] = [];

        $columns['xc4EntityId'] = [];

        return $columns;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return 'p.productid AS `xc4EntityId`,'
            . 'p.productcode AS `sku`,'
            . 'p.distribution AS `attachments`,'
            . 'p.provider AS `attachmentProvider`,'
            . '"" AS `attachmentsTitle`,'
            . '"" AS `attachmentsDescription`,'
            . '"Y" AS `attachmentsPrivate`,'
            . '"N" AS `shippable`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}products AS p";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = 'p.distribution <> ""';

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result .= " AND p.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Verification <editor-fold desc="Verification" defaultstate="collapsed">

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'PRODUCT-ATTACH-PATH-INFO' => 'The downloadable product relative path {{path}}',
                'PRODUCT-ATTACH-ZERO-FILE' => 'The downloadable product file {{path}} has zero size',
            ];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    protected function normalizeAttachmentsValue($value)
    {
        static $dirs_checked = [], $is_platinum;
        $result = [];

        if ($value) {
            $provider = $this->currentRowData['attachmentProvider'];

            if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
                $provider = escapeshellarg($provider);
                $file_provider_prefix = '';
            } else {
                $file_provider_prefix = '/userfiles_';
            }

            $path = static::getSitePath();

            $provider_dir_used = $dirs_checked[$provider ?? 0] ?? \Includes\Utils\FileManager::isDirReadable($path . '/files' . $file_provider_prefix . ($provider ?? ''));
            $dirs_checked[$provider ?? 0] = $provider_dir_used;

            if ($path) {
                if ($provider_dir_used && is_null($is_platinum)) {
                    $prefix = static::getTablePrefix();
                    $is_platinum = static::getCellData("SELECT COUNT(*) FROM {$prefix}modules WHERE module_name='Simple_Mode' AND active='N'") ?: false;
                }
                if ($provider_dir_used && $provider && !preg_match('%^/userfiles_[\d]+/%', $value[0]) && $is_platinum) {
                    $path = $path . '/files' . ($file_provider_prefix . $provider) . '/' . $value[0];
                } else {
                    $path = $path . '/files' . '/' . $value[0];
                }

                $result = [ \Includes\Utils\FileManager::makeRelativePath(LC_DIR_ROOT, $path) ];
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'attachments' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     */
    protected function importAttachmentsColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        parent::importAttachmentsColumn($model, $this->normalizeAttachmentsValue($value), $column);
    }

    /**
     * @param \XLite\Model\Product $model
     * @param string               $value
     * @param array                $column
     */
    protected function importAttachmentProviderColumn(\XLite\Model\Product $model, $value, array $column)
    {
    }

    // }}} </editor-fold>

    // {{{ Verification helpers

    protected function verifyValueAsFile($value)
    {
        $res = parent::verifyValueAsFile($value);

        // For Some Reason Parent VerifyValueAsFile Can Deal With Files Within Xc5 Root Dir Only
        if (
            $res === false
            && \Includes\Utils\FileManager::isReadable(LC_DIR_ROOT . $value)
        ) {
            $res = true;
        }

        return $res;
    }

    // }}}

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}products"
            . ' WHERE distribution <> "" LIMIT 1'
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
        return static::t('Migrating E-Goods');
    }
}
