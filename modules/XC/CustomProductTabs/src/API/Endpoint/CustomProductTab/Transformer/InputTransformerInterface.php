<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\Transformer;

use XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO\Input as InputDTO;
use XC\CustomProductTabs\API\Resource\CustomProductTab;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): CustomProductTab;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
