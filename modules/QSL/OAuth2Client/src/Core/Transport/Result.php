<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Transport;

/**
 * Result
 */
class Result extends \XLite\Base
{
    /**
     * @var boolean
     */
    public $result;

    /**
     * @var \League\OAuth2\Client\Token\AccessToken
     */
    public $token;

    /**
     * @var \QSL\OAuth2Client\Core\Transport\User
     */
    public $user;
}
