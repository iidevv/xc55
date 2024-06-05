<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\DTO;

use CDev\VolumeDiscounts\Validator\Constraint as VolumeDiscountAssert;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class VolumeDiscountOutput
{
    /**
     * @Assert\NotBlank
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var float
     */
    public float $value = 0.0;

    /**
     * @Assert\NotBlank
     * @VolumeDiscountAssert\VolumeDiscountType()
     * @var string
     */
    public string $type = '%';

    /**
     * @var float|null
     */
    public ?float $subtotal_range_begin;

    /**
     * @var int|null
     */
    public ?int $membership;

    /**
     * @var int[]
     */
    public array $zones = [];

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $date_range_begin;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $date_range_end;
}
