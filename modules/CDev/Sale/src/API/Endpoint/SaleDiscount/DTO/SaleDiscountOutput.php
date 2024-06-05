<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\API\Endpoint\SaleDiscount\DTO;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class SaleDiscountOutput
{
    /**
     * @Assert\NotBlank
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @var float
     */
    public float $value = 0.0;

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
     * @var bool
     */
    public bool $show_in_separate_section = false;

    /**
     * @var string
     */
    public string $meta_description_type = 'A';

    /**
     * @var bool
     */
    public bool $specific_products = false;

    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $meta_tags = '';

    /**
     * @var string
     */
    public string $meta_description = '';

    /**
     * @var string
     */
    public string $meta_title = '';

    /**
     * @var string
     */
    public string $clean_url = '';

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
    public array $products = [];

    /**
     * @var int[]
     */
    public array $categories = [];
}
