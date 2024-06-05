<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\Tag;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\Tag\OrderCustomerSatisfactionSurveyTagOutput as OutputDTO;
use QSL\CustomerSatisfaction\Model\Tag;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Tag $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->name = $object->getName();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Tag;
    }
}
