<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ChangeAttributeValues
{

    /**
     * Check amount for all cart items
     *
     * @return void
     */
    protected function checkItemsAmount()
    {
        parent::checkItemsAmount();

        $this->getCart()->updateLinkedProducts();
    }
}