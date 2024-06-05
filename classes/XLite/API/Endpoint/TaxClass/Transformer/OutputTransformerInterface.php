<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\TaxClass\Transformer;

use XLite\API\Endpoint\TaxClass\DTO\TaxClassOutput;
use XLite\Model\TaxClass;

interface OutputTransformerInterface
{
    public function transform(TaxClass $object, string $to, array $context = []): TaxClassOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
