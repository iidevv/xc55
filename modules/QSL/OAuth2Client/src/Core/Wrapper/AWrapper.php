<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

use XLite\InjectLoggerTrait;

/**
 * Abstract wrapper
 */
abstract class AWrapper extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    public const RETURN_INVALID_STATE  = 'invalid_state';
    public const RETURN_NOT_CONFIGURED = 'not_configured';
    public const RETURN_FAIL           = 'fail';
    public const RETURN_TOKEN_FAIL     = 'token_fail';
    public const RETURN_USER_FAIL      = 'user_fail';
    public const RETURN_SUCCESS        = 'success';

    /**
     * Assigned provider
     *
     * @var \QSL\OAuth2Client\Model\Provider
     */
    protected $provider;

    /**
     * Get form fields
     *
     * @return array
     */
    abstract public function getFormFields();

    /**
     * Validate form fields
     *
     * @param array $fields Fields
     *
     * @return array
     */
    public function validate(array $fields)
    {
        return [];
    }

    /**
     * Check - is configured or not
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $result = true;
        foreach ($this->getFormFields() as $name => $field) {
            if (!empty($field[\XLite\View\Model\AModel::SCHEMA_REQUIRED]) && !$this->provider->getSetting($name)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Check - is visible or not
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->isConfigured();
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        return [
            'provider' => $this->provider,
        ];
    }

    /**
     * Assign provider
     *
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return static
     */
    public function assignProvider(\QSL\OAuth2Client\Model\Provider $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Field is setting or not
     *
     * @param string $name Field name
     *
     * @return boolean
     */
    public function isSetting($name)
    {
        $fields = $this->getFormFields();

        return isset($fields[$name]) && $name != 'authCallbackURL';
    }

    // {{{ Login widget

    /**
     * Get widget class
     *
     * @return string
     */
    public function getWidgetClass()
    {
        return 'QSL\OAuth2Client\View\Login\Common';
    }

    /**
     * Get auth. request URL
     *
     * @param string URL Return URL
     *
     * @return string
     */
    public function getRequestURL($url)
    {
        $provider = $this->getInternalProvider();
        $authurl = $provider->getAuthorizationUrl($this->getAuthorizationUrlArguments());

        $this->getLogger('QSL-OAuth2Client')->debug('Assemble authorize URL', [
            'URL' => $authurl,
            'State' => $provider->getState()
        ]);

        $this->setState('state', $provider->getState())
            ->setState('returnURL', $url);

        return $authurl;
    }

    /**
     * Get arguments for getAuthorizationUrl() method
     *
     * @return array
     */
    protected function getAuthorizationUrlArguments()
    {
        return [];
    }

    // }}}

    // {{{ Internal provider

    /**
     * Internal provider
     *
     * @var \League\OAuth2\Client\Provider\AbstractProvider
     */
    protected $internal_provider;

    /**
     * Define internal provider
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     * @throws \InvalidArgumentException
     */
    abstract protected function defineInternalProvider();

    /**
     * Get internal provider
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function getInternalProvider()
    {
        if (!$this->internal_provider) {
            $this->internal_provider = $this->defineInternalProvider();
        }

        return $this->internal_provider;
    }

    /**
     * Process return
     *
     * @return \QSL\OAuth2Client\Core\Transport\Result
     */
    public function processReturn()
    {
        $result = new \QSL\OAuth2Client\Core\Transport\Result();

        $state = $this->getState();
        if (!$state || !$state['state'] || $state['state'] != \XLite\Core\Request::getInstance()->state) {
            $this->getLogger('QSL-OAuth2Client')->error('Invalid state', [
                'Session state' => $state['state'],
                'Current state' => \XLite\Core\Request::getInstance()->state,
                'Provider'      => \XLite\Core\Request::getInstance()->provider,
            ]);
            $result->result = static::RETURN_INVALID_STATE;
        } elseif (!$this->isConfigured()) {
            $result->result = static::RETURN_NOT_CONFIGURED;
        } elseif (!$this->isReturnValid()) {
            $this->getLogger('QSL-OAuth2Client')->error('Request failed', [
                'Provider' => \XLite\Core\Request::getInstance()->provider,
            ]);
            $result->result = static::RETURN_FAIL;
        } else {
            $provider = $this->getInternalProvider();

            // Try to get an access token (using the authorization code grant)
            try {
                $result->token = $provider->getAccessToken(
                    'authorization_code',
                    [
                        'code' => \XLite\Core\Request::getInstance()->code,
                    ]
                );
            } catch (\Exception $e) {
                // Failed to get token
                $this->getLogger('QSL-OAuth2Client')->error('Token fail', [
                    'Error'    => $e->getMessage(),
                    'Provider' => \XLite\Core\Request::getInstance()->provider,
                    'trace'    => $e->getTrace(),
                ]);
                $result->result = static::RETURN_TOKEN_FAIL;
                \XLite\Logger::getInstance()->registerException($e);
            }

            // Optional: Now you have a token you can look up a users profile data
            if ($result->token) {
                try {
                    // We got an access token, let's now get the user's details
                    $result->user = $this->normalizeUser($provider->getResourceOwner($result->token), $result->token);
                    $result->result = $result->user->id ? static::RETURN_SUCCESS : static::RETURN_USER_FAIL;
                    if ($result->user->id) {
                        $result->result = static::RETURN_SUCCESS;
                    } else {
                        $result->result = static::RETURN_USER_FAIL;
                        $this->getLogger('QSL-OAuth2Client')->error('User external ID is empty', [
                            'Provider' => \XLite\Core\Request::getInstance()->provider,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Failed to get user details
                    $this->getLogger('QSL-OAuth2Client')->error('User data fail', [
                        'Error'    => $e->getMessage(),
                        'Provider' => \XLite\Core\Request::getInstance()->provider,
                        'trace'    => $e->getTrace(),
                    ]);
                    $result->result = static::RETURN_USER_FAIL;
                }
            }
        }

        return $result;
    }

    /**
     * Get redirect URL
     *
     * @return string
     */
    public function getRedirectURL()
    {
        return $this->provider->getRedirectURL();
    }

    /**
     * Request (authenticated)
     *
     * @param string $url     URL
     * @param string $token   Token
     * @param string $method  Request method OPTIONAL
     * @param array  $options Options OPTIONAL
     *
     * @return mixed
     */
    public function requestAuthenticated($url, $token, $method = \League\OAuth2\Client\Provider\AbstractProvider::METHOD_GET, array $options = [])
    {
        $provider = $this->getInternalProvider();

        return $provider->getResponse($provider->getAuthenticatedRequest($method, $url, $token, $options));
    }

    /**
     * Normalize user
     *
     * @param \League\OAuth2\Client\Provider\ResourceOwnerInterface $resource Resource
     * @param \League\OAuth2\Client\Token\AccessToken               $token    Token
     *
     * @return \QSL\OAuth2Client\Core\Transport\User
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = new \QSL\OAuth2Client\Core\Transport\User();
        $user->id           = $resource->getId();
        $user->token        = $token->getToken();
        $user->refreshToken = $token->getRefreshToken();
        $user->expires      = $token->getExpires();

        $this->getLogger('QSL-OAuth2Client')->debug('Returned resources', [
            'resource' => $resource->toArray(),
            'Provider' => $this->provider->getServiceName(),
        ]);

        return $user;
    }

    /**
     * Check - return is valid or not
     *
     * @return boolean
     */
    protected function isReturnValid()
    {
        return true;
    }

    // }}}

    // {{{ State

    /**
     * Set OAuth2 state
     *
     * @param string $varname Sub variable name
     * @param mixed  $value   Value
     *
     * @return static
     */
    public function setState($varname, $value)
    {
        $data = \XLite\Core\Session::getInstance()->oauth2state;
        if (!is_array($data)) {
            $data = [];
        }
        if (!isset($data[$this->provider->getServiceName()])) {
            $data[$this->provider->getServiceName()] = [];
        }
        $data[$this->provider->getServiceName()][$varname] = $value;

        \XLite\Core\Session::getInstance()->oauth2state = $data;

        return $this;
    }

    /**
     * Get OAuth2 state
     *
     * @return array
     */
    public function getState()
    {
        $data = \XLite\Core\Session::getInstance()->oauth2state;
        if (!is_array($data)) {
            $data = [];
        }

        return $data[$this->provider->getServiceName()] ?? null;
    }

    /**
     * Unset state
     *
     * @return static
     */
    public function unsetState()
    {
        $data = \XLite\Core\Session::getInstance()->oauth2state;
        if (!is_array($data)) {
            $data = [];
        }
        if (isset($data[$this->provider->getServiceName()])) {
            unset($data[$this->provider->getServiceName()]);
        }

        \XLite\Core\Session::getInstance()->oauth2state = $data;

        return $this;
    }

    // }}}

    // {{{ Custom properties

    /**
     * Is customer properties or not?
     *
     * @param string $name Property name
     *
     * @return boolean
     */
    public function isCustomProperty($name)
    {
        return false;
    }

    /**
     * Get property value
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function getCustomProperty($name)
    {
        return null;
    }

    /**
     * Set property value
     *
     * @param string $name  Property name
     * @param mixed  $value Value
     *
     * @return static
     */
    public function setCustomProperty($name, $value)
    {
        return $this;
    }

    // }}}
}
