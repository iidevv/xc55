<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\API\Endpoint\SaleDiscount\Transformer;

use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as OutputDTO;
use CDev\Sale\Model\SaleDiscount as Model;

interface OutputTransformerInterface
{
    public function transform(Model $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
