<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderHistory\Transformer\Detail;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use XLite\API\Endpoint\OrderHistory\DTO\Detail\OrderHistoryDetailOutput as OutputDTO;
use XLite\Model\OrderHistoryEventsData;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param OrderHistoryEventsData $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->name = $object->getName();
        $dto->value = $object->getValue();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof OrderHistoryEventsData;
    }
}
