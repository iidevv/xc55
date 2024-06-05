<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Coupon\DTO;

use CDev\Coupons\Validator\Constraint as CouponAssert;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class CouponInput
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min="4", max="16")
     * @var string
     */
    public string $code = '';

    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @Assert\PositiveOrZero()
     * @var float
     */
    public float $value = 0.0;

    /**
     * @Assert\NotBlank
     * @CouponAssert\CouponType()
     * @var string
     */
    public string $type = '%';

    /**
     * @var string
     */
    public string $comment = '';

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $date_range_begin = null;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $date_range_end = null;

    /**
     * @Assert\PositiveOrZero()
     * @var float
     */
    public float $total_range_begin = 0;

    /**
     * @Assert\PositiveOrZero()
     * @var float
     */
    public float $total_range_end = 0;

    /**
     * @Assert\PositiveOrZero()
     * @var int
     */
    public int $uses_limit = 0;

    /**
     * @Assert\PositiveOrZero()
     * @var int
     */
    public int $uses_limit_per_user = 0;

    /**
     * @var bool
     */
    public bool $single_use = false;

    /**
     * @var bool
     */
    public bool $specific_products = false;

    /**
     * @var int[]
     */
    public array $product_classes = [];

    /**
     * @var int[]
     */
    public array $memberships = [];

    /**
     * @var int[]
     */
    public array $zones = [];

    /**
     * @var int[]
     */
    public array $products = [];

    /**
     * @var int[]
     */
    public array $categories = [];
}
