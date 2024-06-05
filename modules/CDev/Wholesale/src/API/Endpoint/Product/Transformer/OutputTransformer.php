<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\Product\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Output as OutputDTO;
use XLite\API\Endpoint\Product\Transformer\OutputTransformer as ExtendedOutputTransformer;
use CDev\Wholesale\API\Endpoint\Product\DTO\ProductOutput as ModuleOutputDTO;
use XLite\Model\Product as Model;

/**
 * @Extender\Mixin
 */
class OutputTransformer extends ExtendedOutputTransformer
{
    /**
     * @param Model $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::transform($object, $to, $context);

        $dto->apply_sale_to_wholesale = $object->getApplySaleToWholesale();

        return $dto;
    }
}
