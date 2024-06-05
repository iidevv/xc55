<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\API\Endpoint\Tag\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

class TagInput
{
    /**
     * @Assert\Length(min=1, max=255)
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="sale"}
     *     }
     * )
     * @var string
     */
    public string $name;
}
