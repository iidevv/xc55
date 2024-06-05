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

class CouponOutput
{
    /**
     * @Assert\NotBlank
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max="16")
     * @var string
     */
    public string $code;

    /**
     * @var bool
     */
    public bool $enabled;

    /**
     * @var float
     */
    public float $value;

    /**
     * @Assert\NotBlank
     * @CouponAssert\CouponType()
     * @var string
     */
    public string $type;

    /**
     * @var string
     */
    public string $comment;

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

    /**
     * @var float|null
     */
    public ?float $total_range_begin;

    /**
     * @var float|null
     */
    public ?float $total_range_end;

    /**
     * @var int|null
     */
    public ?int $uses_limit;

    /**
     * @var int|null
     */
    public ?int $uses_limit_per_user;

    /**
     * @var bool
     */
    public bool $single_use;

    /**
     * @var bool
     */
    public bool $specific_products;

    /**
     * @var int[]
     */
    public array $product_classes;

    /**
     * @var int[]
     */
    public array $memberships;

    /**
     * @var int[]
     */
    public array $zones;

    /**
     * @var int[]
     */
    public array $products;

    /**
     * @var int[]
     */
    public array $categories;
}
