<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Question controller
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class ProductQuestionWithMultiVendors extends \QSL\ProductQuestions\Controller\Admin\ProductQuestion
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $product = $this->getModelForm()->getModelObject()->getProduct();

        $vendorAccessGranted = \XLite\Core\Auth::getInstance()->isPermissionAllowed('[vendor] manage catalog')
            && (!$product || $product->isOfCurrentVendor());

        return parent::checkACL() || $vendorAccessGranted;
    }
}
