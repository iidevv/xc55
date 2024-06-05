<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Input
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $name = '';

    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $content = '';

    /**
     * @var string
     */
    public string $brief_info = '';
}
