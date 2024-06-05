<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use XLite\API\Endpoint\Product\Validator\Constraints\Date;
use XLite\API\Endpoint\Product\Validator\Constraints\Memberships;
use XLite\API\Endpoint\Product\Validator\Constraints\ProductClass;
use XLite\API\Endpoint\Product\Validator\Constraints\TaxClass;

class Input
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=32)
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="0001"}
     *     }
     * )
     * @var string
     */
    public string $sku = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Product name"}
     *     }
     * )
     * @var string
     */
    public string $name = '';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Full description"}
     *     }
     * )
     * @var string
     */
    public string $description = '';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Description"}
     *     }
     * )
     * @var string
     */
    public string $brief_description = '';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"=""}
     *     }
     * )
     * @var string
     */
    public string $meta_tags = '';

    /**
     * @Assert\Choice(choices = {"A", "C"})
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="string",
     *             "enum"={"A", "C"},
     *             "example"="A"
     *         }
     *     }
     * )
     * @var string
     */
    public string $meta_description_type = 'A';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"=""}
     *     }
     * )
     * @var string
     */
    public string $meta_description = '';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"=""}
     *     }
     * )
     * @var string
     */
    public string $meta_title = '';

    /**
     * @Assert\PositiveOrZero()
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="9.99"}
     *     }
     * )
     * @var float
     */
    public float $price = 0.0000;

    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @Assert\PositiveOrZero()
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="1.5"}
     *     }
     * )
     * @var float
     */
    public float $weight = 0.0000;

    /**
     * @var bool
     */
    public bool $separate_box = false;

    /**
     * @Assert\PositiveOrZero()
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="1.5"}
     *     }
     * )
     * @var float
     */
    public float $width = 0.0000;

    /**
     * @Assert\PositiveOrZero()
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="1.5"}
     *     }
     * )
     * @var float
     */
    public float $length = 0.0000;

    /**
     * @Assert\PositiveOrZero()
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="1.5"}
     *     }
     * )
     * @var float
     */
    public float $height = 0.0000;

    /**
     * @var bool
     */
    public bool $free_shipping = false;

    /**
     * @var bool
     */
    public bool $taxable = true;

    /**
     * @Date
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="2021-10-22T00:00:00+00:00"}
     *     }
     * )
     * @var string|null
     */
    public ?string $arrival_date = null;

    /**
     * @var bool
     */
    public bool $inventory_traceable = true;

    /**
     * @Assert\PositiveOrZero()
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="1000"}
     *     }
     * )
     * @var int
     */
    public int $amount = 1000;

    /**
     * @ProductClass
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Fashion"}
     *     }
     * )
     * @var string|null
     */
    public ?string $product_class = null;

    /**
     * @TaxClass
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="VAT"}
     *     }
     * )
     * @var string|null
     */
    public ?string $tax_class = null;

    /**
     * @Memberships
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"={"Wholesaler"}}
     *     }
     * )
     * @var string[]
     */
    public array $memberships = [];

    /**
     * @var string
     */
    public string $clean_url = '';
}
