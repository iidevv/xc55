<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Tag\Transformer;

use XC\ProductTags\API\Endpoint\Tag\DTO\TagInput as InputDTO;
use XC\ProductTags\Model\Tag;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Tag;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
