<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeGroup\Transformer;

use XLite\API\Endpoint\AttributeGroup\DTO\AttributeGroupOutput as OutputDTO;
use XLite\Model\AttributeGroup;

interface OutputTransformerInterface
{
    public function transform(AttributeGroup $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
