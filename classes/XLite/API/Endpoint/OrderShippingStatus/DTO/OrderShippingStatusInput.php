<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderShippingStatus\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderShippingStatusInput
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=4)
     * @var string
     */
    public string $code;
}
