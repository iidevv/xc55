<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Select\Mapper;

use XLite\Model\AttributeValue\AAttributeValue;

class InputWeightModifierTypeMapper implements InputWeightModifierTypeMapperInterface
{
    public function map(string $type): string
    {
        if ($type === 'percent') {
            return AAttributeValue::TYPE_PERCENT;
        }

        return AAttributeValue::TYPE_ABSOLUTE;
    }
}
