<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\Answer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\Answer\OrderCustomerSatisfactionSurveyAnswerOutput as OutputDTO;
use QSL\CustomerSatisfaction\Model\Answer;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Answer $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->value = $object->getValue();
        $dto->origin_question = $object->getOriginQuestion();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Answer;
    }
}
