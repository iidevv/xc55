<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\Transformer;

use XLite\API\Endpoint\ProfileAddress\DTO\ProfileAddressOutput as OutputDTO;
use XLite\Model\Address;

interface OutputTransformerInterface
{
    public function transform(Address $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
