<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{
    public const FIELD_INCLUDE_SUBCATEGORIES = 'is_stickers_included_subcategories';
    public const FIELD_CATEGORY_STICKERS     = 'category_stickers';

    /**
     * @param array $params
     * @param array $sections
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $schema = [];
        foreach ($this->schemaDefault as $k => $v) {
            $schema[$k] = $v;
            if ($k === 'memberships') {
                $schema[self::FIELD_CATEGORY_STICKERS] = [
                    static::SCHEMA_CLASS => 'QSL\ProductStickers\View\FormField\Select\CategoryStickers',
                    static::SCHEMA_LABEL => 'Product stickers',
                    static::SCHEMA_REQUIRED => false,
                    static::FIELD_INCLUDE_SUBCATEGORIES => $this->getModelObject()->isStickersIncludedSubcategories()
                ];
            }
        }
        $this->schemaDefault = $schema;

        parent::__construct($params, $sections);
    }

    /**
     * @param array $data
     */
    protected function setModelProperties(array $data)
    {
        \XLite\Model\Product::removeProductStickerCache();
        $data[self::FIELD_CATEGORY_STICKERS] = $data[self::FIELD_CATEGORY_STICKERS] ?? [];
        $data[self::FIELD_INCLUDE_SUBCATEGORIES] = $this->getPostedData(self::FIELD_INCLUDE_SUBCATEGORIES);
        parent::setModelProperties($data);
    }
}
