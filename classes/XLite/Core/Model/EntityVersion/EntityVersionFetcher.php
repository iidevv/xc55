<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityVersion;

/**
 * Fetches just entity versions without pulling up whole entities
 */
class EntityVersionFetcher extends BulkEntityVersionFetcher
{
    public function __construct($entityType)
    {
        parent::__construct($entityType, []);
    }
}
