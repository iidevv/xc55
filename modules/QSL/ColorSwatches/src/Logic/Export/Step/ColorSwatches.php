<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Logic\Export\Step;

/**
 * Color swatches
 */
class ColorSwatches extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');
    }

    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'swatches.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'color'    => [],
            'position' => [],
            'image'    => [],
        ];

        $columns += $this->assignI18nColumns(
            [
                'name' => [],
            ]
        );

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'sku' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getColorColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'color');
    }

    /**
     * Get column value for 'price' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPositionColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'position');
    }

    /**
     * Get column value for 'images' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getImageColumnValue(array $dataset, $name, $i)
    {
        return $this->formatImageModel($dataset['model']->getImage());
    }

    /**
     * @inheritdoc
     */
    protected function copyResource(\XLite\Model\Base\Storage $storage, $subdirectory)
    {
        if ($storage instanceof \XLite\Model\Base\Image) {
            $subdirectory .= LC_DS . 'swatches';
        }

        return parent::copyResource($storage, $subdirectory);
    }

    // }}}
}
