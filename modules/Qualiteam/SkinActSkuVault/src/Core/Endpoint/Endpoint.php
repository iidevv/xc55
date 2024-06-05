<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Endpoint;

use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\API\APIService;
use ReflectionClass;
use XCart\Container;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Request;

abstract class Endpoint
{
    use ExecuteCachedTrait;

    private APIService $apiService;

    /** @var string $url */
    private $url;

    private GetTokens $getTokens;

    /** @var mixed cached data */
    private $data;

    /**
     * @param APIService $apiService
     */
    public function __construct(APIService $apiService, string $url, GetTokens $getTokens)
    {
        $this->apiService = $apiService;
        $this->url        = $url;
        $this->getTokens  = $getTokens;
    }

    protected function getClassName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function getData(): array
    {
        if ($this->data === null) {
            $cookie = Request::getInstance()->getCookieData();
            $encodeCookie = $cookie[$this->getClassName()];
            $data = json_decode($encodeCookie, true);
            if (empty($data)) {
                $data = $this->retrieveData();
                if ($data) {
                    Request::getInstance()->setCookie(
                        $this->getClassName(),
                        json_encode($data, JSON_THROW_ON_ERROR),
                        60
                    );
                }
            }
            $this->data = $data;
        }

        return $this->data;
    }

    protected function retrieveData()
    {
        $baseUrl = Container::getContainer()->getParameter('skuvault.baseUrl');
        $url     = $baseUrl . $this->url;

        $tokens = $this->getTokens->getData();

        try {
            return $this->apiService->sendRequest(
                'POST',
                $url,
                [
                    'body' => json_encode([
                      'PageNumber'  => 0,
                      'TenantToken' => $tokens['TenantToken'],
                      'UserToken'   => $tokens['UserToken'],
                  ]),
                ]
            );

        } catch (APIException $e) {

            throw new EndpointException('Request to ' . $this->getClassName() . ' endpoint failed', 0, $e);
        }
    }
}
