<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Event\Service;

use Symfony\Contracts\EventDispatcher\Event;

final class FixturesPostLoadEvent extends Event
{
    public const NAME = 'xcart.service.post-fixtures-load';
}
