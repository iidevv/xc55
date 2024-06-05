<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\OrderCustomerSatisfactionSurveyOutput as OutputDTO;
use QSL\CustomerSatisfaction\Model\Survey;
use QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\Answer\OutputTransformerInterface as AnswerOutputTransformer;
use QSL\CustomerSatisfaction\API\Endpoint\Order\Transformer\Survey\Tag\OutputTransformerInterface as TagOutputTransformer;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected AnswerOutputTransformer $answerTransformer;

    protected TagOutputTransformer $tagTransformer;

    /**
     * @required
     */
    public function setAnswerTransformer(AnswerOutputTransformer $answerTransformer): void
    {
        $this->answerTransformer = $answerTransformer;
    }

    /**
     * @required
     */
    public function setTagTransformer(TagOutputTransformer $tagTransformer): void
    {
        $this->tagTransformer = $tagTransformer;
    }

    /**
     * @param Survey $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->rating = $object->getRating();
        $dto->status = $object->getStatus();
        $dto->email_date = $object->getEmailDate()
            ? new DateTimeImmutable('@' . $object->getEmailDate())
            : null;
        $dto->init_date = new DateTimeImmutable('@' . $object->getInitDate());
        $dto->feedback_date = $object->getFeedbackDate()
            ? new DateTimeImmutable('@' . $object->getFeedbackDate())
            : null;
        $dto->feedback_processed_date = $object->getFeedbackProcessedDate()
            ? new DateTimeImmutable('@' . $object->getFeedbackProcessedDate())
            : null;
        $dto->comments = $object->getComments();
        $dto->customer_message = $object->getCustomerMessage();
        $dto->manager_profile_id = $object->getManager()
            ? $object->getManager()->getProfileId()
            : null;
        $dto->customer_profile_ids = $object->getCustomer()
            ? $object->getCustomer()->getProfileId()
            : null;
        $dto->hash_key = $object->getHashKey();
        $dto->filled = $object->getFilled();

        $dto->answers = [];
        foreach ($object->getAnswers() as $answer) {
            $dto->answers[] = $this->answerTransformer->transform($answer, $to, $context);
        }

        $dto->tags = [];
        foreach ($object->getTags() as $tag) {
            $dto->answers[] = $this->tagTransformer->transform($tag, $to, $context);
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Survey;
    }
}
