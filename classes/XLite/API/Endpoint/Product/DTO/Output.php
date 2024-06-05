<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\DTO;

use XLite\API\Endpoint\Membership\DTO\MembershipOutput;
use XLite\API\Endpoint\ProductImage\DTO\ImageOutput;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassOutput;
use XLite\API\Endpoint\TaxClass\DTO\TaxClassOutput;

class Output
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $sku;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var string
     */
    public string $brief_description;

    /**
     * @var string
     */
    public string $meta_tags;

    /**
     * @var string
     */
    public string $meta_description;

    /**
     * @var string
     */
    public string $meta_title;

    /**
     * @var float
     */
    public float $price;

    /**
     * @var bool
     */
    public bool $enabled;

    /**
     * @var float
     */
    public float $weight;

    /**
     * @var bool
     */
    public bool $separate_box;

    /**
     * @var float
     */
    public float $width;

    /**
     * @var float
     */
    public float $length;

    /**
     * @var float
     */
    public float $height;

    /**
     * @var bool
     */
    public bool $free_shipping;

    /**
     * @var bool
     */
    public bool $taxable;

    /**
     * @var string
     */
    public string $create_date;

    /**
     * @var string
     */
    public string $update_date;

    /**
     * @var string
     */
    public string $arrival_date;

    /**
     * @var bool
     */
    public bool $inventory_traceable;

    /**
     * @var int
     */
    public int $amount;

    /**
     * @var ProductClassOutput|null
     */
    public ?ProductClassOutput $product_class;

    /**
     * @var TaxClassOutput|null
     */
    public ?TaxClassOutput $tax_class;

    /**
     * @var MembershipOutput[]
     */
    public array $memberships;

    /**
     * @var string
     */
    public string $clean_url;

    /**
     * @var ImageOutput[]
     */
    public array $images = [];
}
