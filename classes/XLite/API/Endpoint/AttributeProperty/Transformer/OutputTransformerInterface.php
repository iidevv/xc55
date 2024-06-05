<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeProperty\Transformer;

use XLite\API\Endpoint\AttributeProperty\DTO\AttributePropertyOutput as OutputDTO;
use XLite\Model\AttributeProperty as Model;

interface OutputTransformerInterface
{
    public function transform(Model $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
