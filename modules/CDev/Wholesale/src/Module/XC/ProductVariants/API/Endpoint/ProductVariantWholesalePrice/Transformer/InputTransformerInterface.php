<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\Transformer;

use CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\DTO\ProductVariantWholesalePriceInput as InputDTO;
use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice as Model;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Model;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
