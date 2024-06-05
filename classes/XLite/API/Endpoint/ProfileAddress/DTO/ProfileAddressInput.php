<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XLite\API\Endpoint\ProfileAddress\DTO\CustomField\ProfileAddressCustomFieldInput;

class ProfileAddressInput
{
    /**
     * @var bool
     */
    public bool $is_billing = false;

    /**
     * @var bool
     */
    public bool $is_shipping = false;

    /**
     * @var bool
     */
    public bool $is_work = false;

    /**
     * @Assert\Choice(choices = {"R","C"})
     * @var string
     */
    public string $type = 'R';

    /**
     * @var string
     */
    public string $title = '';

    /**
     * @var string
     */
    public string $firstname = '';

    /**
     * @var string
     */
    public string $lastname = '';

    /**
     * @var string
     */
    public string $phone = '';

    /**
     * @var string
     */
    public string $street = '';

    /**
     * @Assert\Length(max=32)
     * @var string
     */
    public string $zipcode = '';

    /**
     * @var string
     */
    public string $city = '';

    /**
     * @Assert\Length(min=2, max=2)
     * @var string|null
     */
    public ?string $country_code = null;

    /**
     * @Assert\PositiveOrZero()
     * @var int|null
     */
    public ?int $state = 0;

    /**
     * @var string
     */
    public string $state_name = '';

    /**
     * @var ProfileAddressCustomFieldInput[]
     */
    public array $custom_fields = [];
}
