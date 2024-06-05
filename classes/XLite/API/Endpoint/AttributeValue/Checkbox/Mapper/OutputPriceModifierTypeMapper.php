<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Checkbox\Mapper;

use XLite\Model\AttributeValue\AAttributeValue;

class OutputPriceModifierTypeMapper implements OutputPriceModifierTypeMapperInterface
{
    public function map(string $type): string
    {
        if ($type === AAttributeValue::TYPE_PERCENT) {
            return 'percent';
        }

        return 'absolute';
    }
}
