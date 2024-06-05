<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use XLite\API\Endpoint\Order\DTO\OrderOutput as OutputDTO;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use XLite\Model\Order;

class OutputTransformer extends OutputTransformerAbstract implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Order $object
     *
     * @throws Exception
     *
     * @return OutputDTO|BaseOutput
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = $this->basicTransform(new OutputDTO(), $object, $to, $context);

        $dto->order_number = $object->getOrderNumber();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Order;
    }
}
