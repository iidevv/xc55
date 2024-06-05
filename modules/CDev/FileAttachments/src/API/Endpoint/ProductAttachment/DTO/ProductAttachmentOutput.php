<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductAttachmentOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @var string
     */
    public string $title;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     * @var string
     */
    public string $description;

    /**
     * @var int
     */
    public int $position;

    /**
     * @var string
     */
    public string $access;

    /**
     * @Assert\Url()
     * @var string
     */
    public string $url = '';
}
