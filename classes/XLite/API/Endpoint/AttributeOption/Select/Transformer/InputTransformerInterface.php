<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeOption\Select\Transformer;

use XLite\API\Endpoint\AttributeOption\Select\DTO\AttributeOptionSelectInput as InputDTO;
use XLite\Model\AttributeOption as Model;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Model;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
