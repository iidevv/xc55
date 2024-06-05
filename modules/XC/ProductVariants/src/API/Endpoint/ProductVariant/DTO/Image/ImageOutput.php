<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\ProductVariant\DTO\Image;

class ImageOutput
{
    /**
     * @var string
     */
    public string $alt;

    /**
     * @var string
     */
    public string $url;

    /**
     * @var int
     */
    public int $width;

    /**
     * @var int
     */
    public int $height;
}
