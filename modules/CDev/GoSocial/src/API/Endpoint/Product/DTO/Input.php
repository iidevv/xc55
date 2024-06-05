<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\Product\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Input extends \XLite\API\Endpoint\Product\DTO\Input
{
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
    public string $og_meta_tags_type = 'A';

    /**
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"=""}
     *     }
     * )
     * @var string
     */
    public string $og_meta_tags = '';
}
