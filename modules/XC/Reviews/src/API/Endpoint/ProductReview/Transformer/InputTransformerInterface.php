<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\API\Endpoint\ProductReview\Transformer;

use XC\Reviews\API\Endpoint\ProductReview\DTO\ProductReviewInput as InputDTO;
use XC\Reviews\Model\Review as Model;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Model;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
