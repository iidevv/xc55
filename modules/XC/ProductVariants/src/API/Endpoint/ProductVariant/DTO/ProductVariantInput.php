<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\ProductVariant\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductVariantInput
{
    /**
     * @var float
     */
    public float $price = 0;

    /**
     * @var bool
     */
    public bool $default_price = false;

    /**
     * @var int
     */
    public int $amount = 0;

    /**
     * @var bool
     */
    public bool $default_amount = false;

    /**
     * @Assert\PositiveOrZero
     * @var float
     */
    public float $weight = 0;

    /**
     * @var bool
     */
    public bool $default_weight = false;

    /**
     * @var bool
     */
    public bool $default_variant = false;

    /**
     * @var string|null
     */
    public ?string $sku = null;

    /**
     * @var Image\ImageInput|null
     */
    public ?Image\ImageInput $image = null;

    /**
     * @var int[]
     */
    public array $attribute_checkbox_values = [];

    /**
     * @var int[]
     */
    public array $attribute_select_values = [];
}
