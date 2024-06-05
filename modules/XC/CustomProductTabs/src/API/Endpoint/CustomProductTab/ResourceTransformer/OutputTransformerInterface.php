<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\ResourceTransformer;

use XC\CustomProductTabs\API\Resource\CustomProductTab as CustomProductTabResource;
use XC\CustomProductTabs\Model\Product\CustomGlobalTab;

interface OutputTransformerInterface
{
    public function transform(CustomGlobalTab $object, string $to, array $context = []): CustomProductTabResource;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
