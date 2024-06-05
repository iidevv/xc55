<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Cart\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Cart\DTO\CartOutput as OutputDTO;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract;
use XLite\Model\Cart;

class OutputTransformer extends OutputTransformerAbstract implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @return OutputDTO|BaseOutput
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        return $this->basicTransform(new OutputDTO(), $object, $to, $context);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Cart;
    }
}
