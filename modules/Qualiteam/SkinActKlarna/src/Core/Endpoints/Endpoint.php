<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints;

use Qualiteam\SkinActKlarna\Core\Api\ApiException;
use Qualiteam\SkinActKlarna\Core\Api\ApiServiceInterface;
use Qualiteam\SkinActKlarna\Core\Configuration\Configuration;
use ReflectionClass;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Class endpoint
 */
class Endpoint
{
    use ExecuteCachedTrait;

    /**
     * @var ApiServiceInterface
     */
    protected ApiServiceInterface $apiService;

    /**
     * @var \Qualiteam\SkinActKlarna\Core\Configuration\Configuration
     */
    protected Configuration $configuration;

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
     * @var string
     */
    protected string $path; 

    /**
     * Constructor
     *
     * @param ApiServiceInterface $apiService
     * @param Configuration       $configuration
     * @param string              $method
     */
    public function __construct(
        ApiServiceInterface $apiService,
        Configuration $configuration,
        string $method
    )
    {
        $this->apiService    = $apiService;
        $this->configuration = $configuration;
        $this->method        = $method;
    }

    /**
     * Get data
     *
     * @return array|null
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function getData(): ?array
    {
        return $this->retrieveData();
    }

    /**
     * @return void
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function postData(): void
    {
        $this->retrieveData();
    }

    /**
     * Retrieve data
     *
     * @throws EndpointException
     */
    protected function retrieveData(): ?array
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
        $serviceUrl = $this->configuration->getUrl();
        $path       = $this->getPath();

        return sprintf('%s/%s',
            $serviceUrl,
            $path
        );
    }

    /**
     * Get path for url
     *
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set path for url
     *
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }

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
        return array_merge($this->headers, [
            'Content-Type' => 'application/json',
            'Authorization' => $this->getAuth(),
        ]);
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
        $this->headers = array_merge($this->getHeaders(), $additionalHeaders);
    }

    /**
     * @return string
     */
    protected function getAuth(): string
    {
        $username = $this->configuration->getUsername();
        $password = $this->configuration->getPassword();

        $tmp = base64_encode("$username:$password");

        return "Basic $tmp";
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
