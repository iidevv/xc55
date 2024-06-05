<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Api;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class api exception
 */
class ApiException extends Exception
{
    protected $contents;

    protected ?ResponseInterface $response;

    /**
     * @param string                 $message
     * @param int                    $code
     * @param Throwable|null         $previous
     * @param string                 $contents
     * @param ResponseInterface|null $response
     */
    public function __construct(
        string            $message = '',
        int               $code = 0,
        Throwable         $previous = null,
        string            $contents = '',
        ResponseInterface $response = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->contents = $contents;
        $this->response = $response;
    }

    /**
     * Get contents
     *
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * Get response
     *
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
