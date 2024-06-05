<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Override;

class Odnoklassniki extends \Aego\OAuth2\Client\Provider\Odnoklassniki
{
    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(\League\OAuth2\Client\Token\AccessToken $token)
    {
        $param = 'application_key=' . $this->clientPublic
            . '&method=users.getCurrentUser';
        $sign = md5(str_replace('&', '', $param) . md5($token . $this->clientSecret));

        return 'http://api.odnoklassniki.ru/fb.do?' . $param . '&access_token=' . $token . '&sig=' . $sign;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        return ['GET_EMAIL'];
    }
}
