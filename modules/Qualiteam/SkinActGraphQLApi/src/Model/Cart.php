<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model;

use XLite\Core\Session;

/**
 * Class represents an cart
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * 

 */

abstract class Cart extends \XLite\Model\Cart
{
    protected $graphQLAuthToken = '';

    /**
     * @return mixed
     */
    public function getGraphQLAuthToken()
    {
        return $this->graphQLAuthToken;
    }

    /**
     * @param mixed $graphQLAuthToken
     */
    public function setGraphQLAuthToken($graphQLAuthToken)
    {
        $this->graphQLAuthToken = $graphQLAuthToken;
    }

    /**
     * Check if cart exists for provided token
     *
     * @param string $token Cart token
     *
     * @return boolean
     */
    public static function checkCartExistsForToken($token)
    {
        return !\XLite\Core\Database::getRepo('XLite\Model\Cart')->isCartTokenExists($token);
    }

    /**
     * Method to retrieve cart from either profile or session or from token
     *
     * @param string $token Cart token
     *
     * @return \XLite\Model\Cart
     */
    public static function tryRetrieveCartByToken($token = '')
    {
        if (!empty($token)) {
            $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneByCartToken($token);
        } else {
            $cart = null;
        }

        \XLite\Core\Session::getInstance()->{\XLite\Controller\Customer\Checkout::CHECKOUT_AVAIL_FLAG} = time();

        return $cart;
    }

    /**
     * Generate unique cart token for JSON API
     *
     * @return string
     */
    public function generateApiCartToken()
    {
        $generated = false;
        $token     = '';

        while (!$generated) {
            $token = Session::generateToken();

            $generated = $this->getRepository()->isCartTokenExists($token);
        }

        return $token;
    }
}
