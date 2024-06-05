<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\API\Endpoint\Profile\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as ExtendedInput;

/**
 * @Extender\Mixin
 */
class ProfileInput extends ExtendedInput
{
    /**
     * @Assert\Length(max="128")
     * @var string|null
     */
    public ?string $social_login_provider = '';

    /**
     * @Assert\Length(max="128")
     * @var string|null
     */
    public ?string $social_login_id = '';
}
