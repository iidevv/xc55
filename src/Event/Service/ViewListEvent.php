<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Event\Service;

use Symfony\Contracts\EventDispatcher\Event;

final class ViewListEvent extends Event
{
    private array $list;

    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
    }
}
