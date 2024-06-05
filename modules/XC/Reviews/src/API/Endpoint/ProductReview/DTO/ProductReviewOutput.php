<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\API\Endpoint\ProductReview\DTO;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ProductReviewOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $review = '';

    /**
     * @var string|null
     */
    public ?string $response = null;

    /**
     * @Assert\GreaterThanOrEqual("1")
     * @Assert\LessThanOrEqual("5")
     * @var int
     */
    public int $rating = 1;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $addition_date;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $response_date = null;

    /**
     * @var int|null
     */
    public ?int $profile = null;

    /**
     * @var int|null
     */
    public ?int $respondent = null;

    /**
     * @var string
     */
    public string $reviewer_name = '';

    /**
     * @Assert\Choice({0, 1})
     * @var int
     */
    public int $status = 1;

    /**
     * @var bool
     */
    public bool $use_for_meta = false;
}
