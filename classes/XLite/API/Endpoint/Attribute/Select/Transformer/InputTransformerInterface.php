<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Attribute\Select\Transformer;

use XLite\API\Endpoint\Attribute\Select\DTO\AttributeSelectInput as InputDTO;
use XLite\Model\Attribute as Model;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Model;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
