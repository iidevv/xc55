<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\API\Endpoint\Profile\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileOutput as ExtendedOutput;

/**
 * @Extender\Mixin
 */
class ProfileOutput extends ExtendedOutput
{
    /**
     * @var bool
     */
    public bool $gdpr_consent = false;

    /**
     * @Assert\Length(max="32")
     * @var string|null
     */
    public ?string $all_cookies_consent = '';

    /**
     * @Assert\Length(max="32")
     * @var string|null
     */
    public ?string $default_cookies_consent = '';
}
