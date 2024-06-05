<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation;

use XcartGraphqlApi\ContextInterface;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\JWT;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\AuthService;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

/**
 * Class XCartContext
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation
 */
class XCartContext implements ContextInterface
{
    use ExecuteCachedTrait;

    private $jwt;
    private $lng;
    private $cur;

    /**
     * @var AuthService
     */
    private $authService = null;

    /**
     * @var CartService
     */
    private $cartService = null;

    /**
     * @var array
     */
    protected $_token = null;

    /**
     * @var \XLite\Model\Language
     */
    private $_language = null;

    /**
     * @var \XLite\Model\Currency
     */
    private $_currency = null;

    /**
     * @var \XLite\Model\Profile
     */
    private $_profile  = null;

    /**
     * @var \Qualiteam\SkinActGraphQLApi\Model\Device
     */
    private $_device  = null;

    /**
     * XCartContext constructor.
     *
     * @param string $lng
     * @param string $cur
     * @param string $jwt
     */
    public function __construct($lng = '', $cur = '', $jwt = '')
    {
        $this->jwt = $jwt;
        $this->lng = $lng;
        $this->cur = $cur;

        $this->authService = new AuthService();
        $this->cartService = new CartService();

        $this->initSystemContext();
    }

    /**
     * @return AuthService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * @return CartService
     */
    public function getCartService()
    {
        return $this->cartService;
    }

    /**
     * @return array
     *
     * @throws AccessDenied if JWT is provided but incorrect, because this method will likely be called where access level matters,
     * set assess to anonymous otherwise
     */
    public function getAuthToken()
    {
        if ($this->_token === null) {
            if (!empty($this->jwt)) {
                $this->_token = $this->extractTokenFromJwt($this->jwt);
                if (!$this->_token) {
                    throw new AccessDenied();
                }
            } else {
                $this->_token = $this->getAuthService()->generateTokenPayload();
            }
        }

        return $this->_token;
    }

    /**
     * @param string $jwt
     *
     * @return array|null
     */
    public function extractTokenFromJwt($jwt)
    {
        if (!$this->getAuthService()->verifyToken($jwt)) {
            return null;
        }

        try {
            $token = JWT::extract($jwt);
        } catch (\UnexpectedValueException $error) {
            return null;
        }

        return $token;
    }

    /**
     * @return \XLite\Model\Language
     */
    public function getLanguage()
    {
        if ($this->_language === null) {
            /** @var \XLite\Model\Language $language */
            foreach (Database::getRepo('\XLite\Model\Language')->findActiveLanguages() as $language) {
                if ($language->getCode() === $this->lng) {
                    $this->_language = $language;
                    break;
                }
            }

            if ($this->_language === null) {
                $this->_language = \XLite\Core\Session::getInstance()->getLanguage();
            }
        }

        return $this->_language;
    }

    /**
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        if ($this->_currency === null) {
            $this->_currency = \XLite::getInstance()->getCurrency();
        }

        return $this->_currency;
    }

    /**
     * @return bool
     */
    public function hasAdminAccess()
    {
        $token = $this->getAuthToken();

        return $token['access'] === AuthService::ACCESS_ADMIN;
    }

    /**
     * @return bool
     */
    public function hasCustomerAccess()
    {
        return !$this->hasAdminAccess();
    }

    /**
     * @return \XLite\Model\Profile|null
     */
    public function getLoggedProfile()
    {
        if ($this->_profile === null) {
            $token = $this->getAuthToken();

            $this->_profile = isset($token['user_id'])
                ? Database::getRepo('XLite\Model\Profile')->find($token['user_id'])
                : null;
        }

        return $this->_profile;
    }

    /**
     * @return \Qualiteam\SkinActGraphQLApi\Model\Device|null
     */
    public function getLoggedDevice()
    {
        if ($this->_device === null) {
            $token = $this->getAuthToken();

            $this->_device = isset($token['user_id'])
                ? Database::getRepo('\Qualiteam\SkinActGraphQLApi\Model\Device')->find($token['device_id'])
                : null;
        }

        return $this->_device;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->executeCachedRuntime(function() {
            return $this->getAuthService()->verifyToken($this->jwt);
        }, [ 'jwt' => md5($this->jwt) ]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function initSystemContext()
    {
        \XLite::initGQLContext($this);

        if ($this->isAuthenticated() && $this->getLoggedProfile()) {
            $this->getAuthService()->loginProfile($this->getLoggedProfile());
            $this->getCartService()->retrieveCart($this);
        }
    }
}
