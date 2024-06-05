<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Provider;

/**
 * Xcart provider
 */
class Xcart extends \League\OAuth2\Client\Provider\AbstractProvider
{
    use \League\OAuth2\Client\Tool\BearerAuthorizationTrait;

    /**
     * X-Cart base URL
     *
     * @var string
     */
    protected $baseURL;

    /**
     * @inheritdoc
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->baseURL . '?target=oauth2_authorize';
    }

    /**
     * @inheritdoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->baseURL . '?target=oauth2_token';
    }

    /**
     * @inheritdoc
     */
    public function getResourceOwnerDetailsUrl(\League\OAuth2\Client\Token\AccessToken $token)
    {
        return $this->baseURL . '?target=oauth2_resource';
    }

    /**
     * @inheritdoc
     */
    public function getAuthenticatedRequest($method, $url, $token, array $options = [])
    {
        if (is_string($token)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . 'token=' . $token;
        } elseif (is_object($token) && $token instanceof \League\OAuth2\Client\Token\AccessToken) {

            /** @var \League\OAuth2\Client\Token\AccessToken $token */
            $url .= (strpos($url, '?') === false ? '?' : '&') . 'token=' . $token->getToken();
        }

        return parent::getAuthenticatedRequest($method, $url, $token, $options);
    }


    /**
     * @inheritdoc
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function checkResponse(\Psr\Http\Message\ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new \League\OAuth2\Client\Provider\Exception\IdentityProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function createResourceOwner(array $response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return new \QSL\OAuth2Client\Core\Provider\XcartResourceOwner($response);
    }

    /**
     * @inheritdoc
     */
    protected function appendQuery($url, $query)
    {
        $query = trim($query, '?&');

        return $query
            ? ($url . (strpos($url, '?') === false ? '?' : '&') . $query)
            : $url;
    }
}
