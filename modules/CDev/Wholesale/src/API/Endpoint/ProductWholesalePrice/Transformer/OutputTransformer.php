<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\ProductWholesalePrice\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DTO\ProductWholesalePriceOutput as OutputDTO;
use CDev\Wholesale\Model\WholesalePrice as Model;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Model $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->type = $object->getType();
        $dto->price = $object->getPrice();
        $dto->quantity_range_begin = $object->getQuantityRangeBegin();
        $dto->membership = $object->getMembership() ? $object->getMembership()->getMembershipId() : null;

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
