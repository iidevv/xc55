<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use Includes\Utils\ConfigParser;
use Includes\Utils\URLManager;
use PEAR2\HTTP\Request\Response;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\HTTP\Request;
use XLite\Core\Router;
use XLite\Core\Session;
use XLite\InjectLoggerTrait;
use QSL\CloudSearch\Main;

/**
 * CloudSearch API client
 */
class ServiceApiClient
{
    use InjectLoggerTrait;

    /**
     * CloudSearch service access details
     */
    const CLOUD_SEARCH_URL = 'https://cloudsearch.x-cart.com';
    const CLOUD_SEARCH_REMOTE_IFRAME_URL = '/api/v1/iframe?key=';
    const CLOUD_SEARCH_REGISTER_URL = '/api/v1/register';
    const CLOUD_SEARCH_SEARCH_URL = '/api/v1/search';
    const CLOUD_SEARCH_PLAN_INFO_URL = '/api/v1/plan-info';
    const CLOUD_SEARCH_WEBHOOK_URL = '/api/v1/webhook';

    const SEARCH_REQUEST_TIMEOUT = 5;
    const PLAN_INFO_REQUEST_TIMEOUT = 3;

    const WEBHOOK_TIMEOUT = 3;

    protected static array $resultsCache = [];

    /**
     * Request CloudSearch registration
     */
    public function register(): void
    {
        $requestUrl = $this->getCloudSearchUrl() . static::CLOUD_SEARCH_REGISTER_URL;

        $shopUrl = $this->getShopUrl();

        $shopKey = md5(uniqid(rand(), true));

        Database::getRepo('XLite\Model\TmpVar')->setVar('cloud_search_shop_key', $shopKey);

        $request       = new Request($requestUrl);
        $request->body = [
            'shopUrl'  => $shopUrl,
            'shopKey'  => $shopKey,
            'shopType' => 'xc5',
            'extra'    => 'symfony',
        ];

        $response = $request->sendRequest();

        if ($response && $response->code == 200) {
            $data = json_decode($response->body, true);

            if ($data && !empty($data['apiKey'])) {
                $this->storeApiKey($data['apiKey']);

                Config::updateInstance();
            }

            Database::getRepo('XLite\Model\TmpVar')->removeVar('cloud_search_shop_key');
        }
    }

    /**
     * Search functionality on the product list
     */
    public function search(SearchParametersInterface $params): ?array
    {
        $params = $params->getParameters();

        $paramsHash = md5(serialize($params));

        if (!array_key_exists($paramsHash, self::$resultsCache)) {
            $response = $this->performSearchRequest($params);

            self::$resultsCache[$paramsHash] = $response && $response->code == 200
                ? $this->extractSearchResultsFromResponse($response)
                : null;
        }

        return self::$resultsCache[$paramsHash];
    }

    /**
     * Get CloudSearch service URL that defaults to https://cloudsearch.x-cart.com but can be overridden
     * with CLOUD_SEARCH_URL env var
     */
    protected function getCloudSearchUrl(): string
    {
        return !empty($_SERVER['CLOUD_SEARCH_URL']) ? $_SERVER['CLOUD_SEARCH_URL'] : static::CLOUD_SEARCH_URL;
    }

    /**
     * Get search api endpoint url
     */
    public function getSearchApiUrl(): string
    {
        return $this->getCloudSearchUrl() . static::CLOUD_SEARCH_SEARCH_URL;
    }

    /**
     * Get CloudSearch API key
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return Config::getInstance()->QSL->CloudSearch->api_key;
    }

    /**
     * Get CloudSearch API key
     *
     * @return mixed
     */
    public function getSecretKey()
    {
        return Config::getInstance()->QSL->CloudSearch->secret_key;
    }

    /**
     * Retrieve search results from the response body
     */
    protected function extractSearchResultsFromResponse(Response $response): array
    {
        $input = json_decode($response->body, true);

        $products = $input
            && $input['products']
            && count($input['products']) > 0 ? $input['products'] : [];

        return [
            'products'         => $products,
            'numFoundProducts' => $input['numFoundProducts'],
            'facets'           => $input['facets'],
            'stats'            => $input['stats'],
        ];
    }

    /**
     * Perform product search request (ALL) into the CloudSearch service
     */
    protected function performSearchRequest(array $params): Response
    {
        $request = new Request($this->getSearchApiUrl());

        $request->setAdditionalOption(\CURLOPT_TIMEOUT, self::SEARCH_REQUEST_TIMEOUT);

        $data = [
                'apiKey' => $this->getApiKey(),
                'all'    => 1,
            ] + $params;

        $request->body = json_encode($data);
        $request->verb = 'POST';
        $request->setHeader('Content-Type', 'application/json');

        return $request->sendRequest();
    }

    /**
     * Request CS plan info
     *
     * @return mixed|null
     */
    public function getPlanInfo()
    {
        $apiKey    = $this->getApiKey();
        $secretKey = $this->getSecretKey();

        $requestUrl = $this->getCloudSearchUrl() . static::CLOUD_SEARCH_PLAN_INFO_URL;

        $request = new Request($requestUrl);

        $request->setAdditionalOption(\CURLOPT_TIMEOUT, self::PLAN_INFO_REQUEST_TIMEOUT);

        $request->body = [
            'apiKey'    => $apiKey,
            'secretKey' => $secretKey,
        ];

        $response = $request->sendRequest();

        return $response && $response->code == 200 ? json_decode($response->body, true) : null;
    }

    /**
     * Get CloudSearch dashboard url
     */
    public function getDashboardIframeUrl(string $secretKey, array $params): string
    {
        $features = ['cloud_filters', 'admin_search'];

        return $this->getCloudSearchUrl()
            . static::CLOUD_SEARCH_REMOTE_IFRAME_URL
            . $secretKey
            . '&' . http_build_query($params +
                [
                    'client_features' => $features,
                    'locale'          => Session::getInstance()->getLanguage()->getCode(),
                ]);
    }

    /**
     * Get store url without script part
     */
    protected function getShopUrl(): string
    {
        $router = Router::getInstance();

        if (method_exists($router, 'disableLanguageUrlsTmp')) {
            $router->disableLanguageUrlsTmp();
        }

        $url = URLManager::getShopURL();

        if (method_exists($router, 'releaseLanguageUrlsTmp')) {
            $router->releaseLanguageUrlsTmp();
        }

        $protocol = URLManager::isHTTPS() ? 'https' : 'http';

        $hostDetails = ConfigParser::getOptions(['host_details']);

        if (Main::isMultiDomain() && isset($hostDetails[$protocol . '_host_orig'])) {
            $original_host = $hostDetails[$protocol . '_host_orig'];

            $scheme = parse_url($url, PHP_URL_SCHEME);
            $host   = parse_url($url, PHP_URL_HOST);

            $url = $scheme . '://' . $original_host
                . substr($url, strlen($scheme) + strlen('://') + strlen($host));
        }

        return $url;
    }

    /**
     * Store API key in the DB
     */
    protected function storeApiKey(string $key): void
    {
        Database::getRepo('XLite\Model\Config')->createOption(
            [
                'name'     => 'api_key',
                'category' => 'QSL\CloudSearch',
                'value'    => $key,
            ]
        );
    }

    public function sendWebhookEvent($eventData): void
    {
        $requestUrl = $this->getCloudSearchUrl() . static::CLOUD_SEARCH_WEBHOOK_URL;

        $request = new Request($requestUrl);
        $request->setAdditionalOption(\CURLOPT_TIMEOUT, static::WEBHOOK_TIMEOUT);
        $request->body = json_encode($eventData);
        $request->verb = 'POST';
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('X-Api-Key', $this->getSecretKey());

        $response = $request->sendRequest();

        if (!$response || $response->code !== 200) {
            $this->getLogger('CloudSearchLogs')->error('Webhook error', [
                'requestUrl'       => $requestUrl,
                'requestBody'      => $eventData,
                'exceptionMessage' => $request->getErrorMessage(),
            ]);
        }
    }
}
