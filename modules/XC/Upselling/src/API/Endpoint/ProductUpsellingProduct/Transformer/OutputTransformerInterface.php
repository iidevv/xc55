<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\API\Endpoint\ProductUpsellingProduct\Transformer;

use XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO\ProductUpsellingProductOutput as OutputDTO;
use XC\Upselling\Model\UpsellingProduct as Model;

interface OutputTransformerInterface
{
    public function transform(Model $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
