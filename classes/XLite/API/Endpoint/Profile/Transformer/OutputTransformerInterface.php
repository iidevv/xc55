<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\Transformer;

use XLite\API\Endpoint\Profile\DTO\ProfileOutput as OutputDTO;
use XLite\Model\Profile;

interface OutputTransformerInterface
{
    public function transform(Profile $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
