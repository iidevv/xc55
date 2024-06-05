<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XLite\API\Endpoint\ProfileAddress\DTO\CustomField\ProfileAddressCustomFieldOutput;

class ProfileAddressOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

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
     * @var string
     */
    public string $type = '';

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
     * @var string
     */
    public string $country_code = '';

    /**
     * @var int|null
     */
    public ?int $state = 0;

    /**
     * @var string
     */
    public string $state_name = '';

    /**
     * @var ProfileAddressCustomFieldOutput[]
     */
    public array $custom_fields = [];
}
