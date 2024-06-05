<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActFreeGifts\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class SelectedAttributeValues extends \XLite\View\SelectedAttributeValues
{
    public function getChangeAttributeValuesLink()
    {
        return $this->getItem()->getFreeGift() ? $this->buildURL(
            'change_attribute_values',
            '',
            [
                'source'     => 'cart',
                '_source'    => 'gift',
                'storage_id' => $this->getParam('storage_id'),
                'item_id'    => $this->getItem()->getItemId(),
            ]
        ) : parent::getChangeAttributeValuesLink();
    }
}
