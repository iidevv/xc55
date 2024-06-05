<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $productStickers = [];
        foreach ($object->getProductStickers() as $productSticker) {
            $productStickers[] = $productSticker->getProductStickerId();
        }

        $this->default->productStickers = $productStickers;
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $repo = \XLite\Core\Database::getRepo('QSL\ProductStickers\Model\ProductSticker');
        $object->replaceProductStickersByProductStickers($repo->getListByIdOrName($this->default->productStickers));
    }

    /**
     * @param \XLite\Model\Product $object
     * @param null                 $rawData
     */
    public function afterPopulate($object, $rawData = null)
    {
        \XLite\Model\Product::removeProductStickerCache($object);
        parent::afterPopulate($object, $rawData);
    }
}
