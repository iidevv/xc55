<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\Transformer\BackendTransaction;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTime;
use XLite\API\Endpoint\OrderPaymentTransaction\DTO\BackendTransaction\OrderPaymentTransactionBackendTransactionOutput as OutputDTO;
use XLite\API\Endpoint\OrderPaymentTransaction\Transformer\BackendTransaction\Data\OutputTransformerInterface as DataTransformer;
use XLite\Model\Payment\BackendTransaction;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected DataTransformer $dataTransformer;

    public function __construct(
        DataTransformer $dataTransformer
    ) {
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param BackendTransaction $object
     *
     * @throws \Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->status = $object->getStatus();
        $dto->date = new DateTime('@' . $object->getDate());
        $dto->value = $object->getValue();
        $dto->type = $object->getType();

        $dto->data = [];
        foreach ($object->getData() as $data) {
            $dto->data[] = $this->dataTransformer->transform($data, $to, $context);
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof BackendTransaction;
    }
}
