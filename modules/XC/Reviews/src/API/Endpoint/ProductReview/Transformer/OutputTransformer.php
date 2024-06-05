<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\API\Endpoint\ProductReview\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Exception;
use DateTimeImmutable;
use XC\Reviews\API\Endpoint\ProductReview\DTO\ProductReviewOutput as OutputDTO;
use XC\Reviews\Model\Review as Model;

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
        $dto->review = $object->getReview();
        $dto->response = $object->getResponse();
        $dto->rating = $object->getRating();
        $dto->addition_date = new DateTimeImmutable('@' . $object->getAdditionDate());
        $dto->response_date = $object->getResponseDate()
            ? new DateTimeImmutable('@' . $object->getResponseDate())
            : null;
        $dto->profile = $object->getProfile() ? $object->getProfile()->getProfileId() : null;
        $dto->respondent = $object->getRespondent() ? $object->getRespondent()->getProfileId() : null;
        $dto->reviewer_name = $object->getReviewerName();
        $dto->status = $object->getStatus();
        $dto->use_for_meta = $object->getUseForMeta();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
