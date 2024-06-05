<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\View\FormField\Inline\Input\Text\Integer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ProductQuantity extends \XLite\View\FormField\Inline\Input\Text\Integer\ProductQuantity
{
    protected function isEditable()
    {
        $result = parent::isEditable();

        $product = $this->getEntity();
        if (!$product->isSkippedFromSync()) {
            $result = false;
        }

        return $result;
    }
}
