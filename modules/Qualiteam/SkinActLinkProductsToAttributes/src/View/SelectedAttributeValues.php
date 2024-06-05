<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View;

use XCart\Extender\Mapping\Extender;

/**
 * Selected product attribute values widget
 * @Extender\Mixin
 */
class SelectedAttributeValues extends \XLite\View\SelectedAttributeValues
{

    /**
     * Manage 'change attributes' link on cart page
     *
     * @return boolean
     */
    protected function isChangeAttributesLinkVisible()
    {
        return parent::isChangeAttributesLinkVisible() && !$this->getItem()->isAttributeValueLinked();
    }

}