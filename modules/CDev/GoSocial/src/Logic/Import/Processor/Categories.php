<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Categories extends \XLite\Logic\Import\Processor\Categories
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['useCustomOpenGraphMeta'] = [];
        $columns['openGraphMeta'] = [
            static::COLUMN_IS_TAGS_ALLOWED => true,
            static::COLUMN_IS_TRUSTED      => true,
            static::COLUMN_IS_MULTILINGUAL => true,
        ];

        return $columns;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'USER-USE-OG-META-FMT' => 'Wrong format of UseCustomOpenGraphMeta value',
            ];
    }

    /**
     * Verify 'useCustomOpenGraphMeta' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyUseCustomOpenGraphMeta($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('USER-USE-OG-META-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Import 'useCustomOpenGraphMeta' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param mixed                 $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importUseCustomOpenGraphMetaColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $model->setUseCustomOG($this->normalizeValueAsBoolean($value));
    }

    /**
     * Import 'openGraphMeta' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param mixed                 $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importOpenGraphMetaColumn(\XLite\Model\Category $model, $value, array $column)
    {
        // Backward compatibility for non multilingual
        if (!is_array($value)) {
            return;
        }

        foreach ($value as $code => $metaTags) {
            $model->setEditLanguage($code);

            if (!$model->getUseCustomOG()) {
                $metaTags = $model->getOpenGraphMetaTags(false);
            } elseif (is_array($metaTags)) {
                $metaTags = implode(PHP_EOL, $metaTags);
            }

            $value[$code] = \CDev\GoSocial\Logic\OgMeta::prepareOgMeta($metaTags);

            $model->setEditLanguage(null);
        }

        $this->updateModelTranslations($model, $value, 'ogMeta');
    }
}
