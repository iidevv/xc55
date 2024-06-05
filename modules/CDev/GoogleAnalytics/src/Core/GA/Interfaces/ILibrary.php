<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Interfaces;

use CDev\GoogleAnalytics\Core\GA\AJsList;

interface ILibrary
{
    public function getScriptUrl(): string;

    public function getTagContent(): string;

    public function getJsList(): AJsList;
}
