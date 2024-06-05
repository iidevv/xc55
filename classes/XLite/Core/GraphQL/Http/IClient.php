<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Http;

interface IClient
{
    public const OPTION_HEADERS     = 'headers';
    public const OPTION_POST_FIELDS = 'post_fields';
    public const OPTION_ENDPOINT    = 'endpoint';

    /**
     * Create and send an HTTP request.
     *
     * @param string $method  HTTP method.
     * @param array  $options Request options to apply.
     *
     * @return \XLite\Core\GraphQL\Http\Response
     */
    public function request($method, array $options = []);

    /**
     * Get a client configuration option.
     *
     * @param string|null $option The config option to retrieve.
     *
     * @return mixed
     */
    public function getConfig($option = null);
}
