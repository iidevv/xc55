<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Customer attachments input on product page
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\CustomerAttachments")
 */
abstract class CustomerAttachmentsInput extends \XC\CustomerAttachments\View\Product\CustomerAttachmentsInput
{
    public function isVisible()
    {
        return parent::isVisible() && $this->getProduct();
    }
}
