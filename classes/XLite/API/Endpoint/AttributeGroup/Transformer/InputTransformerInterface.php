<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeGroup\Transformer;

use XLite\API\Endpoint\AttributeGroup\DTO\AttributeGroupInput as InputDTO;
use XLite\Model\AttributeGroup;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): AttributeGroup;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
