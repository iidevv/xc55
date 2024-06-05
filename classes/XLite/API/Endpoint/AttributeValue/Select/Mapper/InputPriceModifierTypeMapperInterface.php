<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Select\Mapper;

interface InputPriceModifierTypeMapperInterface
{
    public function map(string $type): string;
}
