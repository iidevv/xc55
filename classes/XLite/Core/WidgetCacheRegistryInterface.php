<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Widget cache registry provides access to the widget cache on the whole, allowing to clear all its contents.
 */
interface WidgetCacheRegistryInterface
{
    public function deleteAll();
}
