<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductAttribute\Select\Transformer;

use XLite\API\Endpoint\ProductAttribute\Select\DTO\ProductAttributeSelectOutput as OutputDTO;
use XLite\Model\Attribute as Model;

interface OutputTransformerInterface
{
    public function transform(Model $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
