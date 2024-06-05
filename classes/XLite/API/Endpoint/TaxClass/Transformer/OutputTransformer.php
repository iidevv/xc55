<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\TaxClass\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\TaxClass\DTO\TaxClassOutput;
use XLite\Model\TaxClass;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param TaxClass $object
     */
    public function transform($object, string $to, array $context = []): TaxClassOutput
    {
        $output = new TaxClassOutput();
        $output->id = $object->getId();
        $output->name = $object->getName();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === TaxClassOutput::class && $data instanceof TaxClass;
    }
}
