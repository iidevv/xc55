<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Message;

class ResizeImage
{
    private $id;

    private $class;

    public function __construct(int $id = null, string $class = '')
    {
        $this->id    = $id;
        $this->class = $class;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
