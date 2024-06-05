<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderHistory\DTO;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use XLite\API\Endpoint\OrderHistory\DTO\Detail\OrderHistoryDetailOutput as DetailOutput;

class OrderHistoryOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $date;

    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var array
     */
    public array $data;

    /**
     * @var string
     */
    public string $comment;

    /**
     * @var int|null
     */
    public ?int $profile_id;

    /**
     * @var string|null
     */
    public ?string $profile_name;

    /**
     * @var string
     */
    public string $ip;

    /**
     * @var DetailOutput[]
     */
    public array $details;
}
