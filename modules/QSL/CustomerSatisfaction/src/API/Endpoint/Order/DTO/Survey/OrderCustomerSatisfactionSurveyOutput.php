<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class OrderCustomerSatisfactionSurveyOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\PositiveOrZero
     * @var int|null
     */
    public ?int $rating;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="1",max="1")
     * @var string
     */
    public string $status;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $email_date;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $init_date;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $feedback_date;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $feedback_processed_date;

    /**
     * @var string
     */
    public string $comments;

    /**
     * @var string
     */
    public string $customer_message;

    /**
     * @var int|null
     */
    public ?int $manager_profile_id;

    /**
     * @var int|null
     */
    public ?int $customer_profile_id;

    /**
     * @var Answer\OrderCustomerSatisfactionSurveyAnswerOutput[]
     */
    public array $answers;

    /**
     * @var Tag\OrderCustomerSatisfactionSurveyTagOutput[]
     */
    public array $tags;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="64",max="64")
     * @var string
     */
    public string $hash_key;

    /**
     * @var bool
     */
    public bool $filled;
}
