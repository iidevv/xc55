<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\FormField\Select;

class ProductStickers extends \XLite\View\FormField\Select\Multiple
{
    use \XLite\View\FormField\Select\Select2Trait;

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ProductStickers/form_field/select/input_select2.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' input-product-stickers-select2';
    }

    /**
     * @return array
     */
    protected function getProductStickersList()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('\QSL\ProductStickers\Model\ProductSticker')->findAllProductStickers() as $l) {
            $list[$l->sticker_id] = $l->getName();
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getProductStickersList();
    }
}
