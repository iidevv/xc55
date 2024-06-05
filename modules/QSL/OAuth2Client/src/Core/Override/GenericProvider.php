<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Override;

class GenericProvider extends \League\OAuth2\Client\Provider\GenericProvider
{
    /**
     * @inheritdoc
     */
    protected function appendQuery($url, $query)
    {
        $query = trim($query, '?&');

        return $query
            ? ($url . (strpos($url, '?') === false ? '?' : '&') . $query)
            : $url;
    }
}
