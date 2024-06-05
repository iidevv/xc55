<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductClass\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

class ProductClassInput
{
    /**
     * @Assert\Length(min=1, max=255)
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Apparel"}
     *     }
     * )
     * @var string
     */
    public string $name;
}
