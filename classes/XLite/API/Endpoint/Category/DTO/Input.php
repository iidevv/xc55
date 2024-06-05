<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use XLite\API\Endpoint\Product\Validator\Constraints\Memberships;

class Input
{
    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @var bool
     */
    public bool $show_title = true;

    /**
     * @var int
     */
    public int $position = 0;

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
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="null"}
     *     }
     * )
     * @var int|null
     */
    public ?int $parent = null;

    /**
     * @var string
     */
    public string $clean_url = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Category name"}
     *     }
     * )
     * @var string
     */
    public string $name = '';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Description"}
     *     }
     * )
     * @var string
     */
    public string $description = '';

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
}
