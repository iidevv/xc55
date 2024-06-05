<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductAttribute\Select\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductAttributeSelectOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @var int
     */
    public int $position;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"S", "B"})
     * @var string
     */
    public string $displayMode;
}
