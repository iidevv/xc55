<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\Transformer\BackendTransaction\Data;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\OrderPaymentTransaction\DTO\BackendTransaction\Data\OrderPaymentTransactionBackendTransactionDataOutput as OutputDTO;
use XLite\Model\Payment\BackendTransactionData;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param BackendTransactionData $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getDataId();
        $dto->name = $object->getName();
        $dto->value = $object->getValue();
        $dto->access_level = $object->getAccessLevel();
        $dto->label = $object->getLabel();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof BackendTransactionData;
    }
}
