<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Endpoint;

use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\API\APIService;
use XCart\Container;
use XLite\Core\Config;

class GetTokens
{
    private APIService $apiService;

    /** @var string $url */
    private $url;

    /**
     * @param APIService $apiService
     * @param string $url
     */
    public function __construct(APIService $apiService, string $url)
    {
        $this->apiService = $apiService;
        $this->url        = $url;
    }

    public function getData(): array
    {
        $baseUrl = Container::getContainer()->getParameter('skuvault.baseUrl');
        $url = $baseUrl . $this->url;

        try {
            return $this->apiService->sendRequest(
                'POST',
                $url,
                [
                    'body' => json_encode([
                      'Email'    => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_email,
                      'Password' => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_pass,
                  ]),
                ]
            );

        } catch (APIException $e) {
            throw new EndpointException('Request to GetTokens endpoint failed', 0, $e);
        }
    }
}
