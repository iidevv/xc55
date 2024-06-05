<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Client;

class WithXidCookie extends AClient
{
    /**
     * @var array
     */
    private $cookie;

    /**
     * @param \GuzzleHttp\Client                  $httpClient
     * @param \XLite\Core\GraphQL\ResponseBuilder $responseBuilder
     * @param array                               $cookie
     */
    public function __construct($httpClient, $responseBuilder, $cookie)
    {
        parent::__construct($httpClient, $responseBuilder);

        $this->cookie = $cookie;
    }

    protected function prepareOptions(array $options)
    {
        $options['cookies'] = \GuzzleHttp\Cookie\CookieJar::fromArray(
            $this->cookie,
            parse_url($this->httpClient->getConfig('base_uri'), PHP_URL_HOST)
        );

        return parent::prepareOptions($options);
    }
}
