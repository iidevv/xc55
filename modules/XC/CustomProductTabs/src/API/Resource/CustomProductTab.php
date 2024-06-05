<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Resource;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Symfony\Component\Validator\Constraints as Assert;
use XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO\Input as CustomProductTabInput;
use XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO\Output as CustomProductTabOutput;

/**
 * @ApiPlatform\ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     shortName="Custom Product Tab",
 *     input=CustomProductTabInput::class,
 *     output=CustomProductTabOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/custom_product_tabs/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/custom_product_tabs/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/custom_product_tabs/{id}",
 *             "identifiers"={"id"},
 *         }
 *     },
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/custom_product_tabs",
 *             "identifiers"={"id"},
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/custom_product_tabs",
 *             "identifiers"={"id"},
 *         }
 *     }
 * )
 */
class CustomProductTab
{
    /**
     * @Assert\Positive()
     * @ApiPlatform\ApiProperty(
     *     identifier=true,
     *     attributes={
     *         "openapi_context"={"example"="1", "minimum"="1"}
     *     }
     * )
     * @var int|null
     */
    public ?int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     * @ApiPlatform\ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Shipping info"}
     *     }
     * )
     * @var string
     */
    public string $name;

    /**
     * @Assert\NotBlank()
     * @ApiPlatform\ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="content"}
     *     }
     * )
     * @var string
     */
    public string $content;

    /**
     * @ApiPlatform\ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="info"}
     *     }
     * )
     * @var string
     */
    public string $brief_info;
}
