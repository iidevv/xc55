<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\Form\ItemsList;

class ProductSticker extends \XLite\View\Form\ItemsList\AItemsList
{
    protected function getDefaultTarget()
    {
        return 'product_stickers';
    }

    protected function getDefaultAction()
    {
        return 'update';
    }
}
