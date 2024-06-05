<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\Transformer;

use XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO\Output as CustomProductTabOutput;
use XC\CustomProductTabs\API\Resource\CustomProductTab;

interface OutputTransformerInterface
{
    public function transform(CustomProductTab $object, string $to, array $context = []): CustomProductTabOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
