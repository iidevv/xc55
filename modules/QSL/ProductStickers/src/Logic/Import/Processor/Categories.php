<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Categories extends \XLite\Logic\Import\Processor\Categories
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['categoryStickers'] = [
            static::COLUMN_IS_MULTIPLE => true
        ];
        $columns['isStickersIncludedSubcategories'] = [];

        return $columns;
    }

    /**
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages() + [
                'CATEGORY-STICKERS-INCLUDED-SUBCATEGORIES' => 'Wrong format of isStickersIncludedSubcategories value',
            ];
    }

    /**
     * @param \XLite\Model\Category $model
     * @param array                $value
     * @param array                $column
     */
    protected function importCategoryStickersColumn(\XLite\Model\Category $model, array $value, array $column)
    {
        $processed = [];

        if ($value) {
            $repo = \XLite\Core\Database::getRepo('QSL\ProductStickers\Model\ProductSticker');
            foreach ($value as $name) {
                $sticker = $repo->findOneByName($name, false);
                if (!$sticker) {
                    $sticker = $repo->createProductStickerByName($name);
                }
                $processed[] = $sticker;
            }
        }

        $model->setCategoryStickers($processed, true);
    }

    /**
     * @param       $value
     * @param array $column
     */
    protected function verifyIsStickersIncludedSubcategories($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('CATEGORY-STICKERS-INCLUDED-SUBCATEGORIES', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function normalizeIsStickersIncludedSubcategoriesValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }
}
