<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTime;
use XLite\API\Endpoint\OrderPaymentTransaction\DTO\OrderPaymentTransactionOutput as OutputDTO;
use XLite\API\Endpoint\OrderPaymentTransaction\Transformer\BackendTransaction\OutputTransformerInterface as BackendTransactionOutputTransformer;
use XLite\API\Endpoint\OrderPaymentTransaction\Transformer\Data\OutputTransformerInterface as DataOutputTransformer;
use XLite\Model\Payment\Transaction;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected DataOutputTransformer $dataTransformer;

    protected BackendTransactionOutputTransformer $backendTransactionTransformer;

    public function __construct(
        DataOutputTransformer $dataTransformer,
        BackendTransactionOutputTransformer $backendTransactionTransformer
    ) {
        $this->dataTransformer = $dataTransformer;
        $this->backendTransactionTransformer = $backendTransactionTransformer;
    }

    /**
     * @param Transaction $object
     *
     * @throws \Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getTransactionId();
        $dto->name = $object->getMethodName();
        $dto->status = $object->getMethodName();
        $dto->date = new DateTime('@' . $object->getDate());
        $dto->local_name = $object->getMethodLocalName();
        $dto->method_id = $object->getPaymentMethod() ? $object->getPaymentMethod()->getMethodId() : null;
        $dto->note = $object->getNote();
        $dto->public_id = $object->getPublicId();
        $dto->public_txn_id = $object->getPublicTxnId();
        $dto->type = $object->getType();
        $dto->currency = $object->getCurrency()->getCode();
        $dto->value = $object->getValue();

        $dto->data = [];
        foreach ($object->getData() as $data) {
            $dto->data[] = $this->dataTransformer->transform($data, $to, $context);
        }

        $dto->backend_transactions = [];
        foreach ($object->getBackendTransactions() as $data) {
            $dto->backend_transactions[] = $this->backendTransactionTransformer->transform($data, $to, $context);
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Transaction;
    }
}
