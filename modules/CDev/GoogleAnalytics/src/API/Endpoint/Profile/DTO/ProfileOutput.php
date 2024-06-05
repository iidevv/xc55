<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\API\Endpoint\Profile\DTO;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileOutput as ExtendedOutput;

/**
 * @Extender\Mixin
 */
class ProfileOutput extends ExtendedOutput
{
    public string $ga_client_id = '';
}
