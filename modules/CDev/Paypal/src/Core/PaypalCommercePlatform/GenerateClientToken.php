<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\PaypalCommercePlatform;

use PayPalHttp\HttpRequest;

class GenerateClientToken extends HttpRequest
{
    public function __construct()
    {
        parent::__construct("/v1/identity/generate-token", "POST");
        $this->headers["Content-Type"] = "application/json";
    }
}
