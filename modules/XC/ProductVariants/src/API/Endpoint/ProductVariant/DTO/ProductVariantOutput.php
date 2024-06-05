<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\ProductVariant\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductVariantOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var float
     */
    public float $price;

    /**
     * @var bool
     */
    public bool $default_price;

    /**
     * @var int
     */
    public int $amount;

    /**
     * @var bool
     */
    public bool $default_amount;

    /**
     * @var float
     */
    public float $weight;

    /**
     * @var bool
     */
    public bool $default_weight;

    /**
     * @var bool
     */
    public bool $default_variant;

    /**
     * @var string|null
     */
    public ?string $sku;

    /**
     * @var Image\ImageOutput|null
     */
    public ?Image\ImageOutput $image;

    /**
     * @var int[]
     */
    public array $attribute_checkbox_values;

    /**
     * @var int[]
     */
    public array $attribute_select_values;
}
