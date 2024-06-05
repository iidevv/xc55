<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\API\Endpoint\Profile\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as ExtendedInput;

/**
 * @Extender\Mixin
 */
class ProfileInput extends ExtendedInput
{
    /**
     * @Assert\Length(max="25")
     * @var string|null
     */
    public ?string $ava_tax_exemption_number = '';

    /**
     * @Assert\Length(max="4")
     * @var string|null
     */
    public ?string $ava_tax_customer_usage_type = '';
}
