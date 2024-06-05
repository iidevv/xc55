<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\API;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * APIException
 */
class APIException extends Exception
{
    protected $contents;

    protected ResponseInterface $response;

    public function __construct($message = "", $code = 0, Throwable $previous = null, string $contents = '', ResponseInterface $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->contents = $contents;
        $this->response = $response;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
