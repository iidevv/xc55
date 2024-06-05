<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductAttachmentUpdateInput
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @var string
     */
    public string $title = '';

    /**
     * @Assert\Length(max=128)
     * @var string
     */
    public string $description = '';

    /**
     * @var int
     */
    public int $position = 0;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^(?:A|R|\d+)$/Ss")
     * @var string
     */
    public string $access = 'A';
}
