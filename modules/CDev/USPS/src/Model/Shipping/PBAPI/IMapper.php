<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\Model\Shipping\PBAPI;

interface IMapper
{
    /**
     * @param mixed $data
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getMapped();
}
