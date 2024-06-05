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
class Products extends \XLite\Logic\Import\Processor\Products
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['productStickers'] = [
            static::COLUMN_IS_MULTIPLE => true
        ];

        return $columns;
    }

    /**
     * @param \XLite\Model\Product $model
     * @param array                $value
     * @param array                $column
     */
    protected function importProductStickersColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        $processed = [];

        if ($value) {
            $repo = \XLite\Core\Database::getRepo('QSL\ProductStickers\Model\ProductSticker');
            foreach ($value as $name) {
                $sticker = $repo->findOneByName($name, false);
                if (!$sticker) {
                    $sticker = $repo->createProductStickerByName($name);
                    $model->addProductStickers($sticker);
                } else {
                    $model->addProductStickers($sticker);
                }
                $processed[] = $name;
            }
        }

        $toDelete = array_filter($model->getProductStickers()->toArray(), static function ($v) use ($processed) {
            return !in_array($v->getName(), $processed);
        });
        $model->removeProductStickersByProductStickers($toDelete);
    }
}
