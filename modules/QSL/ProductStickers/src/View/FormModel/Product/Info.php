<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $productStickersList = [];
        foreach (\XLite\Core\Database::getRepo('\QSL\ProductStickers\Model\ProductSticker')->findAllProductStickers() as $l) {
            $productStickersList[$l->getProductStickerId()] = $l->getName();
        }

        $schema[self::SECTION_DEFAULT]['productStickers'] = [
            'label'             => static::t('Product stickers'),
            'type'              => 'XLite\View\FormModel\Type\Select2Type',
            'multiple'          => true,
            'choices'           => array_flip($productStickersList),
            'position'          => 743,
        ];


        return $schema;
    }
}
