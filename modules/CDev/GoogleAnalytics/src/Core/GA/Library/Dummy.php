<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Library;

use CDev\GoogleAnalytics\Core\GA\ALibrary;
use CDev\GoogleAnalytics\Core\GA\JsList\Dummy as DummyJsList;

class Dummy extends ALibrary
{
    protected static function tagWidgetClass(): string
    {
        return '';
    }

    protected static function jsListClass(): string
    {
        return DummyJsList::class;
    }

    public function getScriptUrl(): string
    {
        return '';
    }

    public function getTagContent(): string
    {
        return '';
    }
}
