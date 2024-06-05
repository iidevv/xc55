<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\Model\Shipping\PBAPI;

interface ITokenStorage
{
    public const TOKEN_TTL =  36000; // 10 * 60 * 60

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param integer $expiration
     */
    public function setExpiration($expiration);
}
