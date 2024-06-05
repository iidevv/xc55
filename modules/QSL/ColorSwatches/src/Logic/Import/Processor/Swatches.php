<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Logic\Import\Processor;

/**
 * Color swatches import processor
 */
class Swatches extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Swatches imported');
    }

    /**
     * Mark all images as processed
     */
    public function markAllImagesAsProcessed()
    {
        \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Image\Swatch')->unmarkAsProcessed();
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'color'    => [
                static::COLUMN_IS_KEY => true,
                static::COLUMN_LENGTH => 6,
            ],
            'position' => [],
            'image'    => [],
            'name'     => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
        ];
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'SWATCH-COLOR-EMPTY'         => 'Color is empty',
                'SWATCH-COLOR-FMT'           => 'Wring color format',
                'SWATCH-POSITION-FMT'        => 'Wrong position format',
                'SWATCH-NAME-FMT'            => 'Name is empty',
                'SWATCH-IMG-LOAD-FAILED'     => 'Error of image loading. Make sure the "images" directory has write permissions.',
                'SWATCH-IMG-URL-LOAD-FAILED' => "Couldn't download the image {{value}} from URL",
                'SWATCH-IMG-NOT-VERIFIED'    => 'Error of image verification ({{value}}). Make sure you have specified the correct image file or URL.',
            ];
    }

    /**
     * Verify 'color' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyColor($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('SWATCH-COLOR-EMPTY', ['column' => $column, 'value' => $value]);
        } elseif (!preg_match('/^[0-9a-f]{6}$/iSs', trim($value))) {
            $this->addError('SWATCH-COLOR-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'position' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPosition($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !preg_match('/\d+/Ss', trim($value))) {
            $this->addWarning('SWATCH-POSITION-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'image' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyImage($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value) && !$this->verifyValueAsFile($value)) {
            $this->addWarning('GLOBAL-IMAGE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'name' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyName($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('SWATCH-NAME-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'color' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeColorValue($value)
    {
        return $this->normalizeValueAsString($value);
    }

    /**
     * Normalize 'position' value
     *
     * @param mixed $value Value
     *
     * @return float
     */
    protected function normalizePositionValue($value)
    {
        return $this->normalizeValueAsUinteger($value);
    }

    // }}}

    // {{{ Import

    /**
     * Import 'image' value
     *
     * @param \QSL\ColorSwatches\Model\Swatch $model  Swatch
     * @param string                                       $value  Value
     * @param array                                        $column Column info
     */
    protected function importImageColumn(\QSL\ColorSwatches\Model\Swatch $model, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            $file = $this->verifyValueAsLocalURL($value) ? $this->getLocalPathFromURL($value) : $value;

            /* @var \QSL\ColorSwatches\Model\Image\Swatch $image */
            $image = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Image\Swatch')->insert(null, false);

            $success = $this->verifyValueAsURL($file)
                ? $image->loadFromURL($value, true)
                : $image->loadFromLocalFile(LC_DIR_ROOT . $file);

            if ($success) {
                if ($model->getImage()) {
                    \XLite\Core\Database::getEM()->remove($model->getImage());
                    $model->getImage()->setSwatch(null);
                }
                $image->setNeedProcess(true);
                $image->setSwatch($model);
                $model->setImage($image);
            } else {
                \XLite\Core\Database::getEM()->remove($image);

                if ($image->getLoadError() === 'unwriteable') {
                    $this->addError('SWATCH-IMG-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file,
                    ]);
                } elseif ($image->getLoadError() === 'undownloadable') {
                    $this->addWarning('SWATCH-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file,
                    ]);
                } elseif (!$this->verifyValueAsFile($file) && $this->verifyValueAsURL($file)) {
                    $this->addWarning('SWATCH-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $value,
                    ]);
                } else {
                    $this->addWarning('SWATCH-IMG-NOT-VERIFIED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file,
                    ]);
                }
            }
        } elseif ($this->verifyValueAsNull($value) && $model->getImage()) {
            \XLite\Core\Database::getEM()->remove($model->getImage());
            $model->getImage()->setSwatch(null);
            $model->setImage(null);
        }
    }
}
