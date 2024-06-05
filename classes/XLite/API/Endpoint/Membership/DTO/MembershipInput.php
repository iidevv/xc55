<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Membership\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

class MembershipInput
{
    /**
     * @Assert\Length(min=1, max=255)
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={"example"="Wholesaler"}
     *     }
     * )
     * @var string
     */
    public string $name = '';

    /**
     * @var bool
     */
    public bool $enabled = true;
}
