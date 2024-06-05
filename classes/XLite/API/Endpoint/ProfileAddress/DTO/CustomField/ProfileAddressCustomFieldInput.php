<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\DTO\CustomField;

class ProfileAddressCustomFieldInput
{
    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string|null
     */
    public ?string $value = '';
}
