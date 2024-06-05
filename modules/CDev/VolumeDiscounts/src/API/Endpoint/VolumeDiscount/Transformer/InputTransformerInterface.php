<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\Transformer;

use CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\DTO\VolumeDiscountInput as InputDTO;
use CDev\VolumeDiscounts\Model\VolumeDiscount as Model;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Model;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
