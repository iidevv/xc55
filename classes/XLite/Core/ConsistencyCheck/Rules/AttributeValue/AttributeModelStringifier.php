<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\AttributeValue;

use XLite\Model\Attribute;

/**
 * Trait OrderModelStringifier
 * @package XLite\Core\ConsistencyCheck
 */
trait AttributeModelStringifier
{
    public function stringifyModel(Attribute $item): string
    {
        return \XLite\Core\Translation::getInstance()->translate('Attribute') . ' ' . $item->getName();
    }
}
