<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\API\Endpoint\ProductSticker\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductStickerOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    public string $name = '';

    /**
     * @var integer
     */
    public int $position = 0;

    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @var string
     */
    public string $text_color = '';

    /**
     * @var string
     */
    public string $bg_color = '';
}
