<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Tag\Transformer;

use XC\ProductTags\API\Endpoint\Tag\DTO\TagOutput as ProductTagOutput;
use XC\ProductTags\Model\Tag;

interface OutputTransformerInterface
{
    public function transform(Tag $object, string $to, array $context = []): ProductTagOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
