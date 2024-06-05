<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation;

use XcartGraphqlApi\InputInterface;

class Input implements InputInterface
{
    protected $headers = [];

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents('php://input') ?: '';
            $data = json_decode($raw, true) ?: [];
        } else {
            $data = $_REQUEST;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        if (!$this->headers) {
            if (!function_exists('getallheaders')) {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    /* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
                    if (strtolower(substr($name, 0, 5)) == 'http_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                $this->headers = $headers;
            } else {
                $this->headers = getallheaders();
            }
        }

        return $this->headers;
    }
}
