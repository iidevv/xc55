<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\ProductWholesalePrice\Transformer;

use CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DTO\ProductWholesalePriceOutput as OutputDTO;
use CDev\Wholesale\Model\WholesalePrice as Model;

interface OutputTransformerInterface
{
    public function transform(Model $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
