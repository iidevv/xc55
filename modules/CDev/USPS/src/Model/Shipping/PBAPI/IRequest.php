<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\Model\Shipping\PBAPI;

use CDev\USPS\Model\Shipping\PBAPI\Request\RequestException;

interface IRequest
{
    /**
     * @return array
     * @throws RequestException
     */
    public function performRequest();
}
