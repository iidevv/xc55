<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints;

use Qualiteam\SkinActAftership\Core\Api\ApiException;
use Qualiteam\SkinActAftership\Core\Api\ApiService;
use ReflectionClass;
use XCart\Container;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Abstract class endpoint
 */
abstract class AEndpoint
{
    use ExecuteCachedTrait;

    /**
     * @var ApiService
     */
    protected ApiService $apiService;

    /**
     * @var string
     */
    protected string $url;

    /**
     * @var string
     */
    protected string $method;

    /**
     * @var array
     */
    protected array $body = [];

    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * Constructor
     *
     * @param ApiService $apiService
     * @param string     $url
     * @param string     $method
     */
    public function __construct(ApiService $apiService, string $url, string $method)
    {
        $this->apiService = $apiService;
        $this->url        = $url;
        $this->method     = $method;
    }

    /**
     * Get data
     *
     * @return array
     * @throws EndpointException
     */
    public function getData(): array
    {
        return $this->retrieveData();
    }

    /**
     * Retrieve data
     *
     * @throws EndpointException
     */
    protected function retrieveData(): array
    {
        $url     = $this->getUrl();
        $method  = $this->getMethod();
        $options = $this->getOptions();

        try {
            return $this->apiService->sendRequest(
                $method,
                $url,
                $options,
            );
        } catch (ApiException $e) {
            throw new EndpointException('Request to ' . $this->getClassName() . ' endpoint failed. Error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get url
     *
     * @return string
     */
    protected function getUrl(): string
    {
        $serviceUrl = Container::getContainer()->getParameter('aftership.url');
        $url        = $this->url;
        $path       = $this->getPath();
        $params     = $this->getParams();

        return sprintf('%s/%s/%s%s',
            $serviceUrl,
            $url,
            $path,
            $params
        );
    }

    /**
     * Get additional path for url
     *
     * @return string
     */
    abstract protected function getPath(): string;

    /**
     * Get url params
     *
     * @return string
     */
    abstract protected function getParams(): string;

    /**
     * Get method
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $options = [];
        $body    = $this->getBody();
        $headers = $this->getHeaders();

        if (!empty($body)) {
            $options = array_merge($options, ['json' => $body]);
        }

        if (!empty($headers)) {
            $options = array_merge($options, ['headers' => $headers]);
        }

        return $options;
    }

    /**
     * Get body params
     *
     * @return array
     */
    protected function getBody(): array
    {
        return $this->body;
    }

    /**
     * Set body params
     *
     * @param array $params
     *
     * @return void
     */
    public function setBody(array $params): void
    {
        $this->body = $params;
    }

    /**
     * Get headers params
     *
     * @return array
     */
    protected function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set additional headers
     *
     * @param array $additionalHeaders
     *
     * @return void
     */
    public function setHeaders(array $additionalHeaders): void
    {
        $this->headers = $additionalHeaders;
    }

    /**
     * Get current class name
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }
}
